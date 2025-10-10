<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportCompaniesFromXLSX extends XLSXIterator
{
    public const int COLUMN_COMPANY_FULL_NAME = 0;
    public const int COLUMN_NIP = 1;
    public const int COLUMN_REGON = 2;
    public const int COLUMN_STREET = 3;
    public const int COLUMN_POSTCODE = 4;
    public const int COLUMN_CITY = 5;
    public const int COLUMN_COUNTRY = 6;
    public const int COLUMN_INDUSTRY_UUID = 7;
    public const int COLUMN_COMPANY_SHORT_NAME = 8;
    public const int COLUMN_COMPANY_INTERNAL_CODE = 9;
    public const int COLUMN_COMPANY_DESCRIPTION = 10;
    public const int COLUMN_PARENT_COMPANY_NIP = 11;
    public const int COLUMN_PHONE = 12;
    public const int COLUMN_EMAIL = 13;
    public const int COLUMN_WEBSITE = 14;
    public const int COLUMN_ACTIVE = 15;

    public const string COLUMN_DYNAMIC_IS_COMPANY_WITH_NIP_ALREADY_EXISTS = '_is_company_already_exists_with_nip';
    public const string COLUMN_DYNAMIC_AGGREGATE_UUID = '_aggregate_uuid';

    private array $errorMessages = [];
    private array $validators;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly CompanyAggregateCreator $companyAggregateCreator,
        private readonly CompanyAggregateUpdater $companyAggregateUpdater,
        private readonly ImportCompaniesPreparer $importCompaniesPreparer,
        private readonly UpdateImportAction $updateImportAction,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly MessageService $messageService,
        private readonly MessageBusInterface $eventBus,
        private readonly ImportCompaniesReferenceLoader $importCompaniesReferenceLoader,
        private readonly iterable $sharedValidators,
        private readonly iterable $companiesValidators,
    ) {
        parent::__construct($this->filePath, $this->translator);

        $this->validators = array_merge(
            iterator_to_array($this->sharedValidators),
            iterator_to_array($this->companiesValidators)
        );
    }

    public function validateRow(array $row, int $index): array
    {
        $this->importCompaniesReferenceLoader->preload($this->import());
        $industries = $this->importCompaniesReferenceLoader->getIndustries();
        $companies = $this->importCompaniesReferenceLoader->getCompanies();

        $this->errorMessages = [];
        foreach ($this->validators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'industries' => $industries,
                    'companies' => $companies,
                ]
            );
            if (null !== $error) {
                $this->errorMessages[] = sprintf('%s - %s', $error, $this->messageService->get('row', [':index' => $index]));
            }
        }

        return $this->errorMessages;
    }

    private function resolveParentUUID(array $row, array $nipMap): ?CompanyUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_COMPANY_NIP] ?? null;
        if (null === $parentRaw) {
            return null;
        }

        $parentNIP = trim((string) $parentRaw);
        if ('' === $parentNIP) {
            return null;
        }

        if (isset($nipMap[$parentNIP])) {
            return $nipMap[$parentNIP];
        }

        $existingParentCompany = $this->companyReaderRepository->getCompanyByNIP($parentNIP);

        return CompanyUUID::fromString($existingParentCompany->getUUID()->toString());
    }

    public function run(Import $import): array
    {
        $errors = $this->validateBeforeImport();

        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent($this->messageService->get('company.import.error', [], 'companies').': '.$error)
                );
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);

            return $this->import();
        } else {
            [$preparedRows, $nipMap] = $this->importCompaniesPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $nipMap);

                $nip = trim((string) $row[self::COLUMN_NIP]);
                $uuid = $nipMap[$nip];

                if (!$row[ImportCompaniesFromXLSX::COLUMN_DYNAMIC_IS_COMPANY_WITH_NIP_ALREADY_EXISTS]) {
                    $this->companyAggregateCreator->create($row, $uuid, $parentUUID);
                } else {
                    $this->companyAggregateUpdater->update($row, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }

        return $preparedRows;
    }
}
