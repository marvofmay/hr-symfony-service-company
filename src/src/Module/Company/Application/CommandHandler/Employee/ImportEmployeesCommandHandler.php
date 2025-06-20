<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\ImportEmployeesCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Employee\EmployeeMultipleCreator;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ImportEmployeesCommandHandler
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private EmployeeReaderInterface $employeeReaderRepository,
        private PositionReaderInterface $positionReaderRepository,
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private RoleReaderInterface $roleReaderRepository,
        private ImportReaderInterface $importReaderRepository,
        private EmployeeMultipleCreator $employeeMultipleCreator,
        private TranslatorInterface $translator,
        private UpdateImportAction $updateImportAction,
        private CacheInterface $cache,
    ) {
    }

    public function __invoke(ImportEmployeesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportEmployeesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->departmentReaderRepository,
            $this->employeeReaderRepository,
            $this->positionReaderRepository,
            $this->contractTypeReaderRepository,
            $this->roleReaderRepository,
            $this->cache
        );

        $this->employeeMultipleCreator->multipleCreate($importer->import());
        $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        // ToDo save notification about DONE import - immediately
    }
}
