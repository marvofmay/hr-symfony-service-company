<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Service\Employee\EmployeeService;

readonly class CreateEmployeeCommandHandler
{
    public function __construct(
        private EmployeeService $employeeService,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private PositionReaderInterface $positionReaderRepository,
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private RoleReaderInterface $roleReaderRepository,
        private EmployeeReaderInterface $employeeReaderRepository
    )
    {
    }

    public function __invoke(CreateEmployeeCommand $command): void
    {
        $employee = new Employee();

        $employee->setFirstName($command->firstName);
        $employee->setLastName($command->lastName);
        $employee->setPESEL($command->pesel);
        $employee->setEmploymentFrom(\DateTime::createFromFormat('Y-m-d', $command->employmentFrom));
        if (null !== $command->employmentTo) {
            $employee->setEmploymentTo(\DateTime::createFromFormat('Y-m-d', $command->employmentTo));
        }
        $employee->setActive($command->active);

        $company = $this->companyReaderRepository->getCompanyByUUID($command->companyUUID);
        if ($company instanceof Company) {
            $employee->setCompany($company);
        }

        $department = $this->departmentReaderRepository->getDepartmentByUUID($command->departmentUUID);
        if ($department instanceof Department) {
            $employee->setDepartment($department);
        }

        $position = $this->positionReaderRepository->getPositionByUUID($command->positionUUID);
        if ($position instanceof Position) {
            $employee->setPosition($position);
        }

        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($command->contractTypeUUID);
        if ($contractType instanceof ContractType) {
            $employee->setContractType($contractType);
        }

        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        if ($role instanceof Role) {
            $employee->setRole($role);
        }

        if (!empty($command->parentEmployeeUUID)) {
            $parentEmployee = $this->employeeReaderRepository->getEmployeeByUUID($command->parentEmployeeUUID);
            if ($parentEmployee instanceof Employee) {
                $employee->setParentEmployee($parentEmployee);
            }
        }

        $this->employeeService->saveEmployeeInDB($employee);
    }
}
