<?php

declare(strict_types=1);

namespace App\Common\Domain\Trait;

use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use ReflectionProperty;

trait RelationsEntityTrait
{
    public static function getRelations(): array
    {
        $reflectionClass = new ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);

        $relations = [];

        foreach ($properties as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $attributeName = $attribute->getName();

                if (in_array($attributeName, [
                    ORM\OneToOne::class,
                    ORM\OneToMany::class,
                    ORM\ManyToOne::class,
                    ORM\ManyToMany::class
                ], true)) {
                    $relations[] = $property->getName();
                }
            }
        }

        return $relations;
    }
}