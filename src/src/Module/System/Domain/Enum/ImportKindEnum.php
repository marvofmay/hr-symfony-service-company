<?php

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum ImportKindEnum: string implements EnumInterface
{
    case IMPORT_COMPANIES = 'import_companies';
    case IMPORT_DEPARTMENTS = 'import_departments';
    case IMPORT_EMPLOYEES = 'import_employees';
    case IMPORT_COMPANY_STRUCTURE = 'import_company_structure';

    public function label(): string
    {
        return match ($this) {
            self::IMPORT_COMPANIES         => 'importCompanies',
            self::IMPORT_DEPARTMENTS       => 'importDepartments',
            self::IMPORT_EMPLOYEES         => 'importEmployees',
            self::IMPORT_COMPANY_STRUCTURE => 'importCompanyStructure',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}