<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

final readonly class EmailValidator
{
    public static function validate(?string $email): ?string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'email.invalid';
        }

        return null;
    }
}
