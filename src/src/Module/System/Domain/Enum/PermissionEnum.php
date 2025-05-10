<?php

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum PermissionEnum: string implements EnumInterface
{
    case CREATE  = 'create';
    case UPDATE  = 'update';
    case DELETE  = 'delete';
    case VIEW    = 'view';
    case LIST    = 'list';
    case IMPORT  = 'import';

    public function label(): string
    {
        return match ($this) {
            self::CREATE  => self::CREATE->value,
            self::UPDATE  => self::UPDATE->value,
            self::DELETE  => self::DELETE->value,
            self::VIEW    => self::VIEW->value,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}