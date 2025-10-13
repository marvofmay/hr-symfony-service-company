<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Factory;

use App\Common\Domain\Interface\XLSXIteratorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Service\Company\CompanyAggregateCreator;
use App\Module\Company\Domain\Service\Company\CompanyAggregateUpdater;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use App\Module\Company\Domain\Service\Company\ImportCompaniesPreparer;
use App\Module\Company\Domain\Service\Company\ImportCompaniesReferenceLoader;
use App\Module\Company\Domain\Service\Department\DepartmentAggregateCreator;
use App\Module\Company\Domain\Service\Department\DepartmentAggregateUpdater;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsPreparer;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsReferenceLoader;
use App\Module\Company\Domain\Service\Employee\EmployeeAggregateCreator;
use App\Module\Company\Domain\Service\Employee\EmployeeAggregateUpdater;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesPreparer;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesReferenceLoader;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ImporterFactory
{
    public function __construct(
        private TranslatorInterface $translator,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private EmployeeReaderInterface $employeeReaderRepository,
        private ImportCompaniesPreparer $importCompaniesPreparer,
        private CompanyAggregateCreator $companyAggregateCreator,
        private CompanyAggregateUpdater $companyAggregateUpdater,
        private ImportDepartmentsPreparer $importDepartmentsPreparer,
        private DepartmentAggregateCreator $departmentAggregateCreator,
        private DepartmentAggregateUpdater $departmentAggregateUpdater,
        private EmployeeAggregateCreator $employeeAggregateCreator,
        private EmployeeAggregateUpdater $employeeAggregateUpdater,
        private ImportEmployeesPreparer $importEmployeesPreparer,
        private UpdateImportAction $updateImportAction,
        private ImportLogMultipleCreator $importLogMultipleCreator,
        private MessageService $messageService,
        private MessageBusInterface $eventBus,
        private ImportCompaniesReferenceLoader $importCompaniesReferenceLoader,
        private ImportDepartmentsReferenceLoader $importDepartmentsReferenceLoader,
        private ImportEmployeesReferenceLoader $importEmployeesReferenceLoader,
        private iterable $importCompaniesValidators,
        private iterable $importDepartmentsValidators,
        private iterable $importEmployeesValidators,
        private EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function getImporter(ImportKindEnum $type, string $filePath, string $fileName): XLSXIteratorInterface
    {
        return match ($type) {
            ImportKindEnum::IMPORT_COMPANIES => new ImportCompaniesFromXLSX(
                sprintf('%s/%s', $filePath, $fileName),
                $this->translator,
                $this->companyReaderRepository,
                $this->companyAggregateCreator,
                $this->companyAggregateUpdater,
                $this->importCompaniesPreparer,
                $this->updateImportAction,
                $this->importLogMultipleCreator,
                $this->messageService,
                $this->eventBus,
                $this->importCompaniesReferenceLoader,
                $this->importCompaniesValidators,
                $this->entityReferenceCache,
            ),
            ImportKindEnum::IMPORT_DEPARTMENTS => new ImportDepartmentsFromXLSX(
                sprintf('%s/%s', $filePath, $fileName),
                $this->translator,
                $this->departmentReaderRepository,
                $this->departmentAggregateCreator,
                $this->departmentAggregateUpdater,
                $this->importDepartmentsPreparer,
                $this->updateImportAction,
                $this->importLogMultipleCreator,
                $this->messageService,
                $this->eventBus,
                $this->importDepartmentsReferenceLoader,
                $this->importDepartmentsValidators,
                $this->entityReferenceCache,
            ),
            ImportKindEnum::IMPORT_EMPLOYEES => new ImportEmployeesFromXLSX(
                sprintf('%s/%s', $filePath, $fileName),
                $this->translator,
                $this->employeeReaderRepository,
                $this->employeeAggregateCreator,
                $this->employeeAggregateUpdater,
                $this->importEmployeesPreparer,
                $this->updateImportAction,
                $this->importLogMultipleCreator,
                $this->messageService,
                $this->eventBus,
                $this->importEmployeesReferenceLoader,
                $this->importEmployeesValidators,
                $this->entityReferenceCache,
            ),
            default => throw new \InvalidArgumentException("Unsupported importer type: $type->value"),
        };
    }
}
