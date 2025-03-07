<?php

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum ImportLogKindEnum: string implements EnumInterface
{
    case IMPORT_INFO   = 'info';
    case IMPORT_ERROR  = 'error';
    case IMPORT_REPORT = 'report';

    public function label(): string
    {
        return match ($this) {
            self::IMPORT_INFO   => 'info',
            self::IMPORT_ERROR  => 'error',
            self::IMPORT_REPORT => 'report',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}