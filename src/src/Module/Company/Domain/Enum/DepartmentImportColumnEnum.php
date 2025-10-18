<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum;

enum DepartmentImportColumnEnum: int
{
    case DEPARTMENT_NAME = 0;
    case DEPARTMENT_INTERNAL_CODE = 1;
    case STREET = 2;
    case POSTCODE = 3;
    case CITY = 4;
    case COUNTRY = 5;
    case PHONE = 6;
    case COMPANY_UUID = 7;
    case DEPARTMENT_DESCRIPTION = 8;
    case PARENT_DEPARTMENT_INTERNAL_CODE = 9;
    case EMAIL = 10;
    case WEBSITE = 11;
    case ACTIVE = 12;

    case DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS = 100;
    case DYNAMIC_AGGREGATE_UUID = 101;

    public function label(): string
    {
        return match ($this) {
            self::DEPARTMENT_NAME => 'departmentName',
            self::DEPARTMENT_INTERNAL_CODE => 'departmentInternalCode',
            self::STREET => 'street',
            self::POSTCODE => 'postcode',
            self::CITY => 'city',
            self::COUNTRY => 'country',
            self::PHONE => 'phone',
            self::COMPANY_UUID => 'companyUUID',
            self::DEPARTMENT_DESCRIPTION => 'departmentDescription',
            self::PARENT_DEPARTMENT_INTERNAL_CODE => 'parentDepartmentInternalCode',
            self::EMAIL => 'email',
            self::WEBSITE => 'website',
            self::ACTIVE => 'active',
            self::DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS => 'isDepartmentWithInternalCodeAlreadyExists',
            self::DYNAMIC_AGGREGATE_UUID => 'aggregateUUID',
        };
    }
}