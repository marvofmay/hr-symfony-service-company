<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData\Data;

use App\Common\Domain\Interface\EnumInterface;

enum RoleEnum: string implements EnumInterface
{
    case SUPER_ADMIN       = 'super_admin';
    case ADMIN             = 'admin';
    case EMPLOYEE          = 'employee';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}