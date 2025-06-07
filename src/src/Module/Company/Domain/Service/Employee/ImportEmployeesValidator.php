<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader\ContractTypeReaderRepository;
use App\Module\System\Domain\Entity\Import;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ImportEmployeesValidator
{
    public function __construct(
        private TranslatorInterface          $translator,
        private DepartmentReaderInterface    $departmentReaderRepository,
        private EmployeeReaderInterface      $employeeReaderRepository,
        private PositionReaderInterface      $positionReaderRepository,
        private ContractTypeReaderRepository $contractTypeReaderRepository,
        private RoleReaderInterface          $roleReaderRepository,
    )
    {
    }

    public function validate(Import $import): array
    {
        $importer = new ImportEmployeesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->departmentReaderRepository,
            $this->employeeReaderRepository,
            $this->positionReaderRepository,
            $this->contractTypeReaderRepository,
            $this->roleReaderRepository,
        );

        return $importer->validateBeforeImport();
    }
}