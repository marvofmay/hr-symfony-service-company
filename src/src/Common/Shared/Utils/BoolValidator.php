<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

final readonly class BoolValidator
{
    public static function validate(mixed $value): ?string
    {
        if (
            null === $value
            || true === $value
            || false === $value
            || 0 === $value
            || 1 === $value
            || '0' === $value
            || '1' === $value
        ) {
            return null;
        }

        return 'boolean.invalid';
    }
}
