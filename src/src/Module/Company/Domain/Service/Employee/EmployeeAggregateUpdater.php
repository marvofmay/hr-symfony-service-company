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
use App\Module\Company\Domain\Enum\EmployeeImportColumnEnum;

final class EmployeeAggregateUpdater extends AggregateAbstract
{
    public function update(array $row, ?EmployeeUUID $parentUUID): void
    {
        $employeeAggregate = $this->employeeAggregateReaderRepository->getEmployeeAggregateByUUID(
            EmployeeUUID::fromString($row[EmployeeImportColumnEnum::DYNAMIC_AGGREGATE_UUID->value])
        );

        $employeeAggregate->update(
            FirstName::fromString($row[EmployeeImportColumnEnum::FIRST_NAME->value]),
            LastName::fromString($row[EmployeeImportColumnEnum::LAST_NAME->value]),
            PESEL::fromString($row[EmployeeImportColumnEnum::PESEL->value]),
            EmploymentFrom::fromString($row[EmployeeImportColumnEnum::EMPLOYMENT_FROM->value]),
            DepartmentUUID::fromString($row[EmployeeImportColumnEnum::DEPARTMENT_UUID->value]),
            PositionUUID::fromString($row[EmployeeImportColumnEnum::POSITION_UUID->value]),
            ContractTypeUUID::fromString($row[EmployeeImportColumnEnum::CONTACT_TYPE_UUID->value]),
            RoleUUID::fromString($row[EmployeeImportColumnEnum::ROLE_UUID->value]),
            Emails::fromArray([$row[EmployeeImportColumnEnum::EMAIL->value]]),
            new Address(
                $row[EmployeeImportColumnEnum::STREET->value],
                $row[EmployeeImportColumnEnum::POSTCODE->value],
                $row[EmployeeImportColumnEnum::CITY->value],
                $row[EmployeeImportColumnEnum::COUNTRY->value]
            ),
            $row[EmployeeImportColumnEnum::EXTERNAL_UUID->value],
            $row[EmployeeImportColumnEnum::INTERNAL_CODE->value],
            (bool) $row[EmployeeImportColumnEnum::ACTIVE->value],
            Phones::fromArray([$row[EmployeeImportColumnEnum::PHONE->value]]),
            $parentUUID,
            $row[EmployeeImportColumnEnum::EMPLOYMENT_TO->value] ? EmploymentTo::fromString($row[EmployeeImportColumnEnum::EMPLOYMENT_TO->value], EmploymentFrom::fromString($row[EmployeeImportColumnEnum::EMPLOYMENT_FROM->value])) : null,
        );

        $this->commitEvents($employeeAggregate->pullEvents(), EmployeeAggregate::class);
    }
}
