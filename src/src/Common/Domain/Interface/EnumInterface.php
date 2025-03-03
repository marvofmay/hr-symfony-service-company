<?php

namespace App\Common\Interface;

namespace App\Common\Domain\Interface;

interface EnumInterface
{
    public function label(): string;

    public static function values(): array;
}
