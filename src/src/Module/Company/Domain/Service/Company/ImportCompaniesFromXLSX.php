<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\BoolValidator;
use App\Common\Shared\Utils\EmailValidator;
use App\Common\Shared\Utils\NIPValidator;
use App\Common\Shared\Utils\REGONValidator;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportCompaniesFromXLSX extends XLSXIterator
{
    public const int COLUMN_COMPANY_FULL_NAME     = 0;
    public const int COLUMN_COMPANY_SHORT_NAME    = 1;
    public const int COLUMN_COMPANY_INTERNAL_CODE = 2;
    public const int COLUMN_COMPANY_DESCRIPTION   = 3;
    public const int COLUMN_PARENT_COMPANY_NIP    = 4;
    public const int COLUMN_INDUSTRY_UUID         = 5;
    public const int COLUMN_NIP                   = 6;
    public const int COLUMN_REGON                 = 7;
    public const int COLUMN_ACTIVE                = 8;
    public const int COLUMN_PHONE                 = 9;
    public const int COLUMN_EMAIL                 = 10;
    public const int COLUMN_WEBSITE               = 11;
    public const int COLUMN_STREET                = 12;
    public const int COLUMN_POSTCODE              = 13;
    public const int COLUMN_CITY                  = 14;
    public const int COLUMN_COUNTRY               = 15;

    private array $errorMessages = [];

    public function __construct(
        private readonly string                   $filePath,
        private readonly TranslatorInterface      $translator,
        private readonly CompanyReaderInterface   $companyReaderRepository,
        private readonly IndustryReaderInterface  $industryReaderRepository,
        private readonly CompanyAggregateCreator  $companyAggregateCreator,
        private readonly CompanyAggregateUpdater  $companyAggregateUpdater,
        private readonly ImportCompaniesPreparer  $importCompaniesPreparer,
        private readonly CacheInterface           $cache,
        private readonly UpdateImportAction       $updateImportAction,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly MessageService           $messageService,
        private readonly MessageBusInterface      $eventBus,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $this->errorMessages = [];

        [
            $fullName,
            $shortName,
            $internalCompanyCode,
            $description,
            $parentUUID,
            $industryUUID,
            $nip,
            $regon,
            $active,
            $phone,
            $email,
            $website,
            $street,
            $postcode,
            $city,
            $country,
        ] = $row + [null, null, null, null, null, null, null, null, false, null, null, null, null, null, null, null];

        $validations = [
            $this->validateCompanyFullName($fullName),
            $this->validateCompanyShortName($shortName),
            $this->validateCompanyDescription($description),
            $this->validateIndustryUUID($industryUUID),
            $this->validateNIP((string)$nip),
            $this->validateREGON((string)$regon),
            $this->validateActive($active),
            $this->validatePhone($phone),
            $this->validateEmail((string)$email),
            $this->validateWebsite($website),
            $this->validateStreet($street),
            $this->validatePostcode($postcode),
            $this->validateCity($city),
            $this->validateCountry($country),
        ];

        foreach ($validations as $errorMessage) {
            if (null !== $errorMessage) {
                $this->errorMessages[] = $errorMessage;
            }
        }

        return $this->errorMessages;
    }

    private function validateCompanyFullName(?string $fullName): ?string
    {
        if (empty($fullName)) {
            return $this->formatErrorMessage('company.fullName.required');
        }

        if (strlen($fullName) < 3) {
            return $this->formatErrorMessage('company.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function validateCompanyShortName(?string $shortName): ?string
    {
        return null;
    }

    private function validateCompanyDescription(?string $description): ?string
    {
        return null;
    }

    private function validateIndustryUUID(?string $industryUUID): ?string
    {
        if (empty($industryUUID)) {
            return $this->formatErrorMessage('company.industryUUID.required');
        }

        $cacheKey = 'import_industry_uuid_' . $industryUUID;

        $industryExists = $this->cache->get($cacheKey, function () use ($industryUUID) {
            return null !== $this->industryReaderRepository->getIndustryByUUID($industryUUID);
        });

        if (!$industryExists) {
            return $this->formatErrorMessage('industry.uuid.notExists', [':uuid' => $industryUUID], 'industries');
        }

        return null;
    }

    private function validateNIP(?string $nip): ?string
    {
        if (null === $nip) {
            return $this->formatErrorMessage('company.nip.required');
        }

        $nip = preg_replace('/\D/', '', $nip ?? '');

        $errorMessage = NIPValidator::validate($nip);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validateREGON(?string $regon): ?string
    {
        if (null === $regon) {
            return $this->formatErrorMessage('company.regon.required');
        }

        $regon = preg_replace('/\D/', '', $regon ?? '');

        $errorMessage = REGONValidator::validate($regon);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validateActive(?int $active): ?string
    {
        $errorMessage = BoolValidator::validate($active);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validatePhone(?string $phone): ?string
    {
        if (null === $phone) {
            return $this->formatErrorMessage('company.contact.phone.required');
        }

        return null;
    }

    private function validateEmail(?string $email): ?string
    {
        if (empty($email)) {
            return $this->formatErrorMessage('company.contact.email.required');
        }

        $errorMessage = EmailValidator::validate($email);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validateWebsite(?string $website): ?string
    {
        // $errorMessage = WebsiteValidator::validate($website);
        // if (null !== $errorMessage) {
        //    return $this->formatErrorMessage($errorMessage, [], 'validators');
        // }

        return null;
    }

    private function validateStreet(?string $street): ?string
    {
        if (null === $street) {
            return $this->formatErrorMessage('company.address.street.required');
        }

        return null;
    }

    private function validatePostcode(?string $postcode): ?string
    {
        if (null === $postcode) {
            return $this->formatErrorMessage('company.address.postcode.required');
        }

        return null;
    }

    private function validateCity(?string $city): ?string
    {
        if (null === $city) {
            return $this->formatErrorMessage('company.address.city.required');
        }

        return null;
    }

    private function validateCountry(?string $country): ?string
    {
        if (null === $country) {
            return $this->formatErrorMessage('company.address.country.required');
        }

        return null;
    }

    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = 'companies'): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            $this->rowIndex
        );
    }

    private function resolveParentUUID(array $row, array $nipMap): ?CompanyUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_COMPANY_NIP] ?? null;
        if ($parentRaw === null) {
            return null;
        }

        $parentNIP = trim((string)$parentRaw);
        if ($parentNIP === '') {
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
                    new LogFileEvent($this->messageService->get('company.import.error', [], 'companies') . ': ' . $error)
                );
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);

            return $this->import();
        } else {
            [$preparedRows, $nipMap] = $this->importCompaniesPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $nipMap);

                $nip = trim((string)$row[ImportCompaniesFromXLSX::COLUMN_NIP]);
                $uuid = $nipMap[$nip];

                if (!$row['_is_company_already_exists_with_nip']) {
                    $this->companyAggregateCreator->create($row, $nip, $uuid, $parentUUID);
                } else {
                    $this->companyAggregateUpdater->update($row, $nip, $uuid, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }

        return $preparedRows;
    }
}
