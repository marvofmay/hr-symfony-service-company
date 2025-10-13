<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportDepartmentsFromXLSX extends XLSXIterator
{
    public const int    COLUMN_DEPARTMENT_NAME = 0;
    public const int    COLUMN_DEPARTMENT_INTERNAL_CODE = 1;
    public const int    COLUMN_STREET = 2;
    public const int    COLUMN_POSTCODE = 3;
    public const int    COLUMN_CITY = 4;
    public const int    COLUMN_COUNTRY = 5;
    public const int    COLUMN_PHONE = 6;
    public const int    COLUMN_COMPANY_UUID = 7;
    public const int    COLUMN_DEPARTMENT_DESCRIPTION = 8;
    public const int    COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE = 9;
    public const int    COLUMN_EMAIL = 10;
    public const int    COLUMN_WEBSITE = 11;
    public const int    COLUMN_ACTIVE = 12;
    public const string COLUMN_DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS = '_is_department_already_exists_with_internal_code';
    public const string COLUMN_DYNAMIC_AGGREGATE_UUID = '_aggregate_uuid';

    private array $errorMessages = [];

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly DepartmentAggregateCreator $departmentAggregateCreator,
        private readonly DepartmentAggregateUpdater $departmentAggregateUpdater,
        private readonly ImportDepartmentsPreparer $importDepartmentsPreparer,
        private readonly UpdateImportAction $updateImportAction,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly MessageService $messageService,
        private readonly MessageBusInterface $eventBus,
        private readonly ImportDepartmentsReferenceLoader $importDepartmentsReferenceLoader,
        private readonly iterable $departmentsValidators,
        private readonly EntityReferenceCache $entityReferenceCache,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row, int $index): array
    {
        $this->importDepartmentsReferenceLoader->preload($this->import());
        $companies = $this->importDepartmentsReferenceLoader->companies;
        $departments = $this->importDepartmentsReferenceLoader->departments;
        $emailsInternalCodes = $this->importDepartmentsReferenceLoader->emailsInternalCodes;

        $this->errorMessages = [];
        foreach ($this->departmentsValidators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'companies' => $companies,
                    'departments' => $departments,
                    'emailsInternalCodes' => $emailsInternalCodes
                ]
            );
            if (null !== $error) {
                $this->errorMessages[] = sprintf('%s - %s', $error, $this->messageService->get('row', [':index' => $index]));
            }
        }

        return $this->errorMessages;
    }

    private function resolveParentUUID(array $row, array $internalCodeMap): ?DepartmentUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE] ?? null;
        if (null === $parentRaw) {
            return null;
        }

        $parentInternalCode = trim((string) $parentRaw);
        if ('' === $parentInternalCode) {
            return null;
        }

        if (isset($internalCodeMap[$parentInternalCode])) {
            return $internalCodeMap[$parentInternalCode];
        }

        $existingParentDepartment = $this->entityReferenceCache->get(
            Department::class,
            $parentInternalCode,
            fn (string $parentInternalCode) => $this->departmentReaderRepository->getDepartmentByInternalCode($parentInternalCode)
        );

        return DepartmentUUID::fromString($existingParentDepartment->getUUID()->toString());
    }

    public function run(Import $import): array
    {
        $errors = $this->validateBeforeImport();

        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent($this->messageService->get('department.import.error', [], 'departments').': '.$error)
                );
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);

            return $this->import();
        } else {
            [$preparedRows, $internalCodeMap] = $this->importDepartmentsPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $internalCodeMap);

                $internalCode = trim((string) $row[self::COLUMN_DEPARTMENT_INTERNAL_CODE]);
                $uuid = $internalCodeMap[$internalCode];

                if (!$row[ImportDepartmentsFromXLSX::COLUMN_DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS]) {
                    $this->departmentAggregateCreator->create($row, $uuid, $parentUUID);
                } else {
                    $this->departmentAggregateUpdater->update($row, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }
        $this->entityReferenceCache->clear();

        return $preparedRows;
    }
}
