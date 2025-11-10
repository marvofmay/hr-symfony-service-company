<?php

declare(strict_types=1);

namespace App\Common\Domain\Trait;

trait ClassNameExtractorTrait
{
    protected function getShortClassName(string $className): string
    {
        return new \ReflectionClass($className)->getShortName();
    }
}