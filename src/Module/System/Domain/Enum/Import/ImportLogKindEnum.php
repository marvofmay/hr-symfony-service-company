<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Import;

use App\Common\Domain\Interface\EnumInterface;

enum ImportLogKindEnum: string implements EnumInterface
{
    case IMPORT_INFO = 'info';
    case IMPORT_ERROR = 'error';

    public function label(): string
    {
        return match ($this) {
            self::IMPORT_INFO => 'info',
            self::IMPORT_ERROR => 'error',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
