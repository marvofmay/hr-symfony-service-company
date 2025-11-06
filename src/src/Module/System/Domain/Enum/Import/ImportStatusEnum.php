<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Import;

use App\Common\Domain\Interface\EnumInterface;

enum ImportStatusEnum: string implements EnumInterface
{
    case PENDING = 'pending';
    case FAILED = 'failed';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::FAILED => 'failed',
            self::DONE => 'done',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
