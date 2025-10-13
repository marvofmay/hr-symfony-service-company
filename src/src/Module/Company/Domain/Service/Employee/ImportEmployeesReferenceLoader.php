<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;

final class ImportEmployeesReferenceLoader
{
    public array  $departments = [] {
        get {
            return $this->departments;
        }
    }
    public array  $positions = [] {
        get {
            return $this->positions;
        }
    }
    public array  $roles         = [] {
        get {
            return $this->roles;
        }
    }
    public array $employees     = [] {
        get {
            return $this->employees;
        }
    }
    public array $contractTypes = [] {
        get {
            return $this->contractTypes;
        }
    }
    public array $emailsPESELs  = [] {
        get {
            return $this->emailsPESELs;
        }
    }

    public function __construct(
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function preload(array $rows): void
    {
        $departmentUUIDs = [];
        $positionUUIDs = [];
        $roleUUIDs = [];
        $contractTypeUUIDs = [];
        $employeePESELs = [];
        $employeeEmails = [];

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
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_PESEL])) {
                $employeePESELs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_PESEL];
            }
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_PARENT_EMPLOYEE_PESEL])) {
                $employeePESELs[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_PARENT_EMPLOYEE_PESEL];
            }
            if (!empty($row[ImportEmployeesFromXLSX::COLUMN_EMAIL])) {
                $employeeEmails[] = (string) $row[ImportEmployeesFromXLSX::COLUMN_EMAIL];
            }
        }

        $departmentUUIDs = array_unique($departmentUUIDs);
        $positionUUIDs = array_unique($positionUUIDs);
        $roleUUIDs = array_unique($roleUUIDs);
        $contractTypeUUIDs = array_unique($contractTypeUUIDs);
        $employeePESELs = array_unique($employeePESELs);
        $employeeEmails = array_unique($employeeEmails);

        $this->departments = $this->mapByUUID($this->departmentReaderRepository->getDepartmentsByUUID($departmentUUIDs));
        $this->positions = $this->mapByUUID($this->positionReaderRepository->getPositionsByUUID($positionUUIDs));
        $this->roles = $this->mapByUUID($this->roleReaderRepository->getRolesByUUID($roleUUIDs));
        $this->contractTypes = $this->mapByUUID($this->contractTypeReaderRepository->getContractTypesByUUID($contractTypeUUIDs));
        $this->employees = $this->mapByPESEL($this->employeeReaderRepository->getEmployeesByPESEL($employeePESELs));
        $this->emailsPESELs = $this->mapByEmail($this->employeeReaderRepository->getEmployeesPESELByEmails($employeeEmails));
    }

    private function mapByUUID(iterable $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[$entity->getUUID()->toString()] = $entity;
            $this->entityReferenceCache->set($entity);
        }

        return $map;
    }

    private function mapByPESEL(iterable $employees): array
    {
        $map = [];
        foreach ($employees as $employee) {
            $map[$employee->getPESEL()] = $employee;
            $this->entityReferenceCache->set($employee);
        }

        return $map;
    }

    private function mapByEmail(iterable $items): array
    {
        $map = [];
        foreach ($items as $item) {
            $map[$item[ContactTypeEnum::EMAIL->value]] = $item[Employee::COLUMN_PESEL];
        }

        return $map;
    }
}
