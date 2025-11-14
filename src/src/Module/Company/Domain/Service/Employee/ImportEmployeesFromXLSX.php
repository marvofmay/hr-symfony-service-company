<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Enum\EmployeeImportColumnEnum;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportLogKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag(name: 'app.importer')]
final class ImportEmployeesFromXLSX extends XLSXIterator
{
    private array $errorMessages = [];

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
        private readonly EmployeeAggregateCreator $employeeAggregateCreator,
        private readonly EmployeeAggregateUpdater $employeeAggregateUpdater,
        private readonly ImportEmployeesPreparer $importEmployeesPreparer,
        private readonly UpdateImportAction $updateImportAction,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly MessageService $messageService,
        #[Autowire(service: 'event.bus')] private MessageBusInterface $eventBus,
        private readonly ImportEmployeesReferenceLoader $importEmployeesReferenceLoader,
        private readonly EntityReferenceCache $entityReferenceCache,
        #[AutowireIterator(tag: 'app.employee.import.validator')] private readonly iterable $importEmployeesValidators,
    ) {
        parent::__construct($this->translator);
    }

    public function getType(): string
    {
        return ImportKindEnum::IMPORT_EMPLOYEES->value;
    }

    public function validateRow(array $row, int $index): array
    {
        $this->importEmployeesReferenceLoader->preload($this->import());

        $departments = $this->importEmployeesReferenceLoader->departments;
        $positions = $this->importEmployeesReferenceLoader->positions;
        $contractTypes = $this->importEmployeesReferenceLoader->contractTypes;
        $roles = $this->importEmployeesReferenceLoader->roles;
        $employees = $this->importEmployeesReferenceLoader->employees;
        $emailsPESELs = $this->importEmployeesReferenceLoader->emailsPESELs;

        $this->errorMessages = [];
        foreach ($this->importEmployeesValidators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'departments' => $departments,
                    'positions' => $positions,
                    'contractTypes' => $contractTypes,
                    'roles' => $roles,
                    'employees' => $employees,
                    'emailsPESELs' => $emailsPESELs,
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
        $parentRaw = $row[EmployeeImportColumnEnum::PARENT_EMPLOYEE_PESEL->value] ?? null;
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

        $existingParentEmployee = $this->entityReferenceCache->get(
            Employee::class,
            $parentPESEL,
            fn (string $parentPESEL) => $this->employeeReaderRepository->getEmployeeByPESEL($parentPESEL)
        );

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

            return $this->import();
        } else {
            [$preparedRows, $peselMap] = $this->importEmployeesPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $peselMap);

                $pesel = trim((string) $row[EmployeeImportColumnEnum::PESEL->value]);
                $uuid = $peselMap[$pesel];

                if (!$row[EmployeeImportColumnEnum::DYNAMIC_IS_EMPLOYEE_WITH_PESEL_ALREADY_EXISTS->value]) {
                    $this->employeeAggregateCreator->create($row, $uuid, $parentUUID);
                } else {
                    $this->employeeAggregateUpdater->update($row, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }
        $this->entityReferenceCache->clear();

        return $preparedRows;
    }
}
