<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

final readonly class PESELValidator
{
    public static function validate(string $pesel): ?string
    {
        if (11 !== strlen($pesel) || !ctype_digit($pesel)) {
            return 'pesel.invalidLength';
        }

        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
        $checksum = self::calculateChecksum($pesel, $weights);

        if ($checksum !== (int) $pesel[10]) {
            return 'pesel.invalid';
        }

        return null;
    }

    private static function calculateChecksum(string $number, array $weights): int
    {
        $sum = 0;
        foreach ($weights as $i => $weight) {
            $sum += $weight * (int) $number[$i];
        }

        $modulo = $sum % 10;

        return (10 - $modulo) % 10;
    }
}
