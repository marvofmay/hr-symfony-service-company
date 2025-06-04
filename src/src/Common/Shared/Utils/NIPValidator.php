<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

final readonly class NIPValidator
{
    public static function validate(string $nip): ?string
    {
        if (strlen($nip) !== 10) {
            return 'nip.invalidLength';
        }

        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += $weights[$i] * (int) $nip[$i];
        }

        $checksum = $sum % 11;

        if ($checksum === 10 || $checksum !== (int) $nip[9]) {
            return 'nip.invalid';
        }

        return null;
    }
}
