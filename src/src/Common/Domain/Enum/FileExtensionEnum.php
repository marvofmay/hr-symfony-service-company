<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum FileExtensionEnum: string implements EnumInterface
{
    case PDF = 'pdf';
    case CSV = 'csv';
    case PNG = 'png';
    case XLSX = 'xlsx';
    case DOC = 'doc';
    case JPEG = 'jpeg';
    case JPG = 'jpg';
    case WEBP = 'webp';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
