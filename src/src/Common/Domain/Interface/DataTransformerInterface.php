<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.data_transformer')]
interface DataTransformerInterface
{
    public static function supports(): string;
}