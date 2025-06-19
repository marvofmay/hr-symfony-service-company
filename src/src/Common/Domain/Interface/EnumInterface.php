<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface EnumInterface
{
    public function label(): string;

    public static function values(): array;
}
