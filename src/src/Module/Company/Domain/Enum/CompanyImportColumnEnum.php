<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum;

enum CompanyImportColumnEnum: int
{
    case COMPANY_FULL_NAME = 0;
    case NIP = 1;
    case REGON = 2;
    case STREET = 3;
    case POSTCODE = 4;
    case CITY = 5;
    case COUNTRY = 6;
    case INDUSTRY_UUID = 7;
    case COMPANY_SHORT_NAME = 8;
    case COMPANY_INTERNAL_CODE = 9;
    case COMPANY_DESCRIPTION = 10;
    case PARENT_COMPANY_NIP = 11;
    case PHONE = 12;
    case EMAIL = 13;
    case WEBSITE = 14;
    case ACTIVE = 15;

    case DYNAMIC_IS_COMPANY_WITH_NIP_ALREADY_EXISTS = 100;
    case DYNAMIC_AGGREGATE_UUID = 101;

    public function label(): string
    {
        return match ($this) {
            self::COMPANY_FULL_NAME => 'companyFullName',
            self::NIP => 'nip',
            self::REGON => 'regon',
            self::STREET => 'street',
            self::POSTCODE => 'postcode',
            self::CITY => 'city',
            self::COUNTRY => 'country',
            self::INDUSTRY_UUID => 'industryUUID',
            self::COMPANY_SHORT_NAME => 'companyShortName',
            self::COMPANY_INTERNAL_CODE => 'companyInternalCode',
            self::COMPANY_DESCRIPTION => 'companyDescription',
            self::PARENT_COMPANY_NIP => 'parentCompanyNIP',
            self::PHONE => 'phone',
            self::EMAIL => 'email',
            self::WEBSITE => 'website',
            self::ACTIVE => 'active',
            self::DYNAMIC_IS_COMPANY_WITH_NIP_ALREADY_EXISTS => 'isCompanyWithNIPAlreadyExists',
            self::DYNAMIC_AGGREGATE_UUID => 'aggregateUUID',
        };
    }
}
