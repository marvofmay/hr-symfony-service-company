<?php

declare(strict_types=1);

namespace App\Common\Domain\Trait;

trait AttributesEntityTrait
{
    public static function getAttributes(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();

        $attributes = [];
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }

        return $attributes;
    }
}
