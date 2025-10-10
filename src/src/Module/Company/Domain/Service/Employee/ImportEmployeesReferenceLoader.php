<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;

final class ImportEmployeesReferenceLoader
{
    private array $departments = [];
    private array $positions = [];
    private array $roles = [];
    private array $employees = [];
    private array $contractTypes = [];

    public function __construct(
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
    ) {
    }

    public function preload(array $rows): void
    {
        $departmentUUIDs = [];
        $positionUUIDs = [];
        $roleUUIDs = [];
        $contractTypeUUIDs = [];
        $employeePESELs = [];

        foreach ($rows as $row) {
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_DEPARTMENT_UUID])) {
                $departmentUUIDs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_DEPARTMENT_UUID];
            }
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_POSITION_UUID])) {
                $positionUUIDs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_POSITION_UUID];
            }
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_ROLE_UUID])) {
                $roleUUIDs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_ROLE_UUID];
            }
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_CONTACT_TYPE_UUID])) {
                $contractTypeUUIDs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_CONTACT_TYPE_UUID];
            }
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_PARENT_EMPLOYEE_PESEL])) {
                $employeePESELs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_PARENT_EMPLOYEE_PESEL];
            }
        }

        $departmentUUIDs = array_unique($departmentUUIDs);
        $positionUUIDs = array_unique($positionUUIDs);
        $roleUUIDs = array_unique($roleUUIDs);
        $contractTypeUUIDs = array_unique($contractTypeUUIDs);
        $employeePESELs = array_unique($employeePESELs);

        $this->departments = $this->mapByUUID($this->departmentReaderRepository->getDepartmentsByUUID($departmentUUIDs));
        $this->positions = $this->mapByUUID($this->positionReaderRepository->getPositionsByUUID($positionUUIDs));
        $this->roles = $this->mapByUUID($this->roleReaderRepository->getRolesByUUID($roleUUIDs));
        $this->contractTypes = $this->mapByUUID($this->contractTypeReaderRepository->getContractTypesByUUID($contractTypeUUIDs));
        $this->employees = $this->mapByPESEL($this->employeeReaderRepository->getEmployeesByPESEL($employeePESELs));
    }

    public function getDepartments(): array
    {
        return $this->departments;
    }

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getContractTypes(): array
    {
        return $this->contractTypes;
    }

    public function getEmployees(): array
    {
        return $this->employees;
    }

    private function mapByUUID(iterable $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[$entity->getUUID()->toString()] = $entity;
        }

        return $map;
    }

    private function mapByPESEL(iterable $employees): array
    {
        $map = [];
        foreach ($employees as $employee) {
            $map[$employee->getPESEL()] = $employee;
        }

        return $map;
    }
}
