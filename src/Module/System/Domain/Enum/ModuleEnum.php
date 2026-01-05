<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum ModuleEnum: string implements EnumInterface
{
    case SYSTEM = 'system';
    case COMPANY = 'company';
    case NOTES = 'notes';
    case DOCUMENTS = 'documents';

    public function label(): string
    {
        return match ($this) {
            self::SYSTEM => self::SYSTEM->value,
            self::COMPANY => self::COMPANY->value,
            self::NOTES => self::NOTES->value,
            self::DOCUMENTS => self::DOCUMENTS->value,
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
