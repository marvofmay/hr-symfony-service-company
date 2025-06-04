<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum ImportKindEnum: string implements EnumInterface
{
    case IMPORT_COMPANY_STRUCTURE = 'import_company_structure';
    case IMPORT_COMPANIES = 'import_companies';
    case IMPORT_DEPARTMENTS = 'import_departments';
    case IMPORT_EMPLOYEES = 'import_employees';
    case IMPORT_ROLES = 'import_roles';
    case IMPORT_INDUSTRIES = 'import_industries';
    case IMPORT_CONTRACT_TYPES = 'import_contract_types';
    case IMPORT_POSITIONS = 'import_positions';

    public function label(): string
    {
        return match ($this) {
            self::IMPORT_COMPANY_STRUCTURE => 'importCompanyStructure',
            self::IMPORT_COMPANIES => 'importCompanies',
            self::IMPORT_DEPARTMENTS => 'importDepartments',
            self::IMPORT_EMPLOYEES => 'importEmployees',
            self::IMPORT_ROLES => 'importRoles',
            self::IMPORT_INDUSTRIES => 'importIndustries',
            self::IMPORT_POSITIONS => 'importPositions',
            self::IMPORT_CONTRACT_TYPES => 'importContractTypes',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}