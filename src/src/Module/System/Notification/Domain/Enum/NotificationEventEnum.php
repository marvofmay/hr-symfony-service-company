<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum NotificationEventEnum: string implements EnumInterface
{
    case COMPANY_IMPORTED_EVENT = 'company_imported_event';
    case DEPARTMENT_IMPORTED_EVENT = 'department_imported_event';
    case EMPLOYEE_IMPORTED_EVENT = 'employee_imported_event';

    public function label(): string
    {
        return match ($this) {
            self::COMPANY_IMPORTED_EVENT => 'import.companyImportedEvent',
            self::DEPARTMENT_IMPORTED_EVENT => 'import.departmentImportedEvent',
            self::EMPLOYEE_IMPORTED_EVENT => 'import.employeeImportedEvent',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}