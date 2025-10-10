<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportEmployeesFromXLSX extends XLSXIterator
{
    public const int COLUMN_FIRST_NAME = 0;
    public const int COLUMN_LAST_NAME = 1;
    public const int COLUMN_PESEL = 2;
    public const int COLUMN_EMAIL = 3;
    public const int COLUMN_PHONE = 4;
    public const int COLUMN_STREET = 5;
    public const int COLUMN_POSTCODE = 6;
    public const int COLUMN_CITY = 7;
    public const int COLUMN_COUNTRY = 8;
    public const int COLUMN_EMPLOYMENT_FROM = 9;
    public const int COLUMN_DEPARTMENT_UUID = 10;
    public const int COLUMN_POSITION_UUID = 11;
    public const int COLUMN_CONTACT_TYPE_UUID = 12;
    public const int COLUMN_ROLE_UUID = 13;
    public const int COLUMN_PARENT_EMPLOYEE_PESEL = 14;
    public const int COLUMN_INTERNAL_CODE = 15;
    public const int COLUMN_EXTERNAL_UUID = 16;
    public const int COLUMN_EMPLOYMENT_TO = 17;
    public const int COLUMN_ACTIVE = 18;

    public const string COLUMN_DYNAMIC_IS_EMPLOYEE_WITH_PESEL_ALREADY_EXISTS = '_is_employee_already_exists_with_pesel';
    public const string COLUMN_DYNAMIC_AGGREGATE_UUID = '_aggregate_uuid';

    private array $errorMessages = [];
    private array $validators;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly EmployeeAggregateCreator $employeeAggregateCreator,
        private readonly EmployeeAggregateUpdater $employeeAggregateUpdater,
        private readonly ImportEmployeesPreparer $importEmployeesPreparer,
        private readonly UpdateImportAction $updateImportAction,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly MessageService $messageService,
        private readonly MessageBusInterface $eventBus,
        private readonly ImportEmployeesReferenceLoader $importEmployeesReferenceLoader,
        private readonly iterable $sharedValidators,
        private readonly iterable $employeesValidators,
    ) {
        parent::__construct($this->filePath, $this->translator);

        $this->validators = array_merge(
            iterator_to_array($this->sharedValidators),
            iterator_to_array($this->employeesValidators)
        );
    }

    public function validateRow(array $row, int $index): array
    {
        $this->importEmployeesReferenceLoader->preload($this->import());

        $departments = $this->importEmployeesReferenceLoader->getDepartments();
        $positions = $this->importEmployeesReferenceLoader->getPositions();
        $contractTypes = $this->importEmployeesReferenceLoader->getContractTypes();
        $roles = $this->importEmployeesReferenceLoader->getRoles();
        $employees = $this->importEmployeesReferenceLoader->getEmployees();

        $this->errorMessages = [];
        foreach ($this->validators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'departments' => $departments,
                    'positions' => $positions,
                    'contractTypes' => $contractTypes,
                    'roles' => $roles,
                    'employees' => $employees,
                ]
            );
            if (null !== $error) {
                $this->errorMessages[] = sprintf('%s - %s', $error, $this->messageService->get('row', [':index' => $index]));
            }
        }

        return $this->errorMessages;
    }

    private function resolveParentUUID(array $row, array $peselMap): ?EmployeeUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_EMPLOYEE_PESEL] ?? null;
        if (null === $parentRaw) {
            return null;
        }

        $parentPESEL = trim((string) $parentRaw);
        if ('' === $parentPESEL) {
            return null;
        }

        if (isset($peselMap[$parentPESEL])) {
            return $peselMap[$parentPESEL];
        }

        $this->importEmployeesReferenceLoader->preload($this->import());
        $employees = $this->importEmployeesReferenceLoader->getEmployees();

        $existingParentEmployee = $employees[$parentPESEL];

        return EmployeeUUID::fromString($existingParentEmployee->getUUID()->toString());
    }

    public function run(Import $import): array
    {
        $errors = $this->validateBeforeImport();

        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent($this->messageService->get('employee.import.error', [], 'employees').': '.$error)
                );
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);

            return $this->import();
        } else {
            [$preparedRows, $peselMap] = $this->importEmployeesPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $peselMap);

                $pesel = trim((string) $row[self::COLUMN_PESEL]);
                $uuid = $peselMap[$pesel];

                if (!$row[ImportEmployeesFromXLSX::COLUMN_DYNAMIC_IS_EMPLOYEE_WITH_PESEL_ALREADY_EXISTS]) {
                    $this->employeeAggregateCreator->create($row, $uuid, $parentUUID);
                } else {
                    $this->employeeAggregateUpdater->update($row, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }

        return $preparedRows;
    }
}
