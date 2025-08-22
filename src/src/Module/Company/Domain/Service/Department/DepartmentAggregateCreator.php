<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Abstract\AggregateAbstract;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\Name;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;

final class DepartmentAggregateCreator extends AggregateAbstract
{
    public function create(array $row, DepartmentUUID $uuid, ?DepartmentUUID $parentUUID): void
    {
        $departmentAggregate = DepartmentAggregate::create(
            CompanyUUID::fromString($row[ImportDepartmentsFromXLSX::COLUMN_COMPANY_UUID]),
            Name::fromString($row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_NAME]),
            $row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_INTERNAL_CODE],
            new Address(
                $row[ImportDepartmentsFromXLSX::COLUMN_STREET],
                $row[ImportDepartmentsFromXLSX::COLUMN_POSTCODE],
                $row[ImportDepartmentsFromXLSX::COLUMN_CITY],
                $row[ImportDepartmentsFromXLSX::COLUMN_COUNTRY]
            ),
            (bool)$row[ImportDepartmentsFromXLSX::COLUMN_ACTIVE],
            $row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_DESCRIPTION],
            Phones::fromArray([$row[ImportDepartmentsFromXLSX::COLUMN_PHONE]]),
            $row[ImportDepartmentsFromXLSX::COLUMN_EMAIL] ? Emails::fromArray([$row[ImportDepartmentsFromXLSX::COLUMN_EMAIL]]) : null,
            $row[ImportDepartmentsFromXLSX::COLUMN_WEBSITE] ? Websites::fromArray([$row[ImportDepartmentsFromXLSX::COLUMN_WEBSITE]]) : null,
            $parentUUID,
            $uuid
        );

        $this->commitEvents($departmentAggregate->pullEvents(), DepartmentAggregate::class);
    }
}