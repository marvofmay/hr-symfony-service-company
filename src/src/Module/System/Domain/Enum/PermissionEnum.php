<?php

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum PermissionEnum: string implements EnumInterface
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case VIEW = 'view';
    case LIST = 'list';
    case IMPORT = 'import';
    case ASSIGN_PERMISSION_TO_ACCESS_ROLE = 'assign_access_to_access_role';
    case ASSIGN_ACCESS_TO_ROLE = 'assign_access_to_role';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}