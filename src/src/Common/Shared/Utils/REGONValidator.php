<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

final readonly class REGONValidator
{
    public static function validate(string $regon): ?string
    {
        if (!in_array(strlen($regon), [9, 14], true)) {
            return 'regon.invalidLength';
        }

        if (strlen($regon) === 9) {
            $weights = [8, 9, 2, 3, 4, 5, 6, 7];
            $checksum = self::calculateChecksum($regon, $weights);

            if ($checksum !== (int) $regon[8]) {
                return 'regon.invalid';
            }

            return null;
        }

        if (strlen($regon) === 14) {
            $weights9 = [8, 9, 2, 3, 4, 5, 6, 7];
            $checksum9 = self::calculateChecksum(substr($regon, 0, 9), $weights9);
            if ($checksum9 !== (int) $regon[8]) {
                return 'regon.invalid';
            }

            $weights14 = [2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8];
            $checksum14 = self::calculateChecksum($regon, $weights14);

            if ($checksum14 !== (int) $regon[13]) {
                return 'regon.invalid';
            }

            return null;
        }

        return 'regon.invalid';
    }

    private static function calculateChecksum(string $number, array $weights): int
    {
        $sum = 0;
        foreach ($weights as $i => $weight) {
            $sum += $weight * (int) $number[$i];
        }

        $checksum = $sum % 11;
        return $checksum === 10 ? 0 : $checksum;
    }
}
