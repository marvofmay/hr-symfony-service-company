<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum MonologChanelEnum: string implements EnumInterface
{
    case MAIN = 'main';
    case EVENT_LOG = 'eventLog';
    case EVENT_STORE = 'eventStore';
    case IMPORT = 'import';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
