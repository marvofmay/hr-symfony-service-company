<?php

namespace App\Common\Interface;

namespace App\Common\Interface;

interface EnumInterface
{
    public function label(): string;

    public static function values(): array;
}