<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

use DateTime;

final readonly class DateFormatValidator
{
    public static function validate(?string $date, string $format): ?string
    {
        if (empty($date)) {
            return 'date.required';
        }

        $phpFormat = self::convertToPhpFormat($format);

        $dt = DateTime::createFromFormat($phpFormat, $date);
        $errors = DateTime::getLastErrors();

        if ($dt === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            return 'date.invalidFormat';
        }

        if ($dt->format($phpFormat) !== $date) {
            return 'date.invalidFormat';
        }

        return null;
    }

    private static function convertToPhpFormat(string $format): string
    {
        return strtr($format, [
            'dd' => 'd',
            'mm' => 'm',
            'yyyy' => 'Y',
        ]);
    }
}
