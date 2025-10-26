<?php

declare(strict_types=1);

namespace App\Common\Shared\Utils;

final readonly class WebsiteValidator
{
    public static function validate(string $website): ?string
    {
        if (!preg_match('#^https?://#i', $website)) {
            return 'website.invalidProtocol';
        }

        if (!filter_var($website, FILTER_VALIDATE_URL)) {
            return 'website.invalid';
        }

        return null;
    }
}
