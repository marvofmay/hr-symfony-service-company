<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Abstract\AggregateAbstract;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\ContractTypeUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentFrom;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentTo;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\FirstName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\LastName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PESEL;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PositionUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\RoleUUID;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;

final class EmployeeAggregateUpdater extends AggregateAbstract
{
    public function update(array $row, ?EmployeeUUID $parentUUID): void
    {
        $employeeAggregate = $this->employeeAggregateReaderRepository->getEmployeeAggregateByUUID(
            EmployeeUUID::fromString($row[ImportEmployeesFromXLSX::COLUMN_DYNAMIC_AGGREGATE_UUID])
        );

        $employeeAggregate->update(
            FirstName::fromString($row[ImportEmployeesFromXLSX::COLUMN_FIRST_NAME]),
            LastName::fromString($row[ImportEmployeesFromXLSX::COLUMN_LAST_NAME]),
            PESEL::fromString($row[ImportEmployeesFromXLSX::COLUMN_PESEL]),
            EmploymentFrom::fromString($row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_FROM]),
            DepartmentUUID::fromString($row[ImportEmployeesFromXLSX::COLUMN_DEPARTMENT_UUID]),
            PositionUUID::fromString($row[ImportEmployeesFromXLSX::COLUMN_POSITION_UUID]),
            ContractTypeUUID::fromString($row[ImportEmployeesFromXLSX::COLUMN_CONTACT_TYPE_UUID]),
            RoleUUID::fromString($row[ImportEmployeesFromXLSX::COLUMN_ROLE_UUID]),
            Emails::fromArray([$row[ImportEmployeesFromXLSX::COLUMN_EMAIL]]),
            new Address(
                $row[ImportEmployeesFromXLSX::COLUMN_STREET],
                $row[ImportEmployeesFromXLSX::COLUMN_POSTCODE],
                $row[ImportEmployeesFromXLSX::COLUMN_CITY],
                $row[ImportEmployeesFromXLSX::COLUMN_COUNTRY]
            ),
            $row[ImportEmployeesFromXLSX::COLUMN_EXTERNAL_UUID],
            $row[ImportEmployeesFromXLSX::COLUMN_INTERNAL_CODE],
            (bool)$row[ImportEmployeesFromXLSX::COLUMN_ACTIVE],
            Phones::fromArray([$row[ImportEmployeesFromXLSX::COLUMN_PHONE]]),
            $parentUUID,
            $row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_TO] ? EmploymentTo::fromString($row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_TO], EmploymentFrom::fromString($row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_FROM])) : null,
        );

        $this->commitEvents($employeeAggregate->pullEvents(), EmployeeAggregate::class);
    }
}