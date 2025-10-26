<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;

class DepartmentUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return DepartmentUUID::class;
    }

    protected function fromString(string $value): DepartmentUUID
    {
        return DepartmentUUID::fromString($value);
    }
}
