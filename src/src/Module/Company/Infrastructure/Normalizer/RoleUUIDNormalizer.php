<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\RoleUUID;

class RoleUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return RoleUUID::class;
    }

    protected function fromString(string $value): RoleUUID
    {
        return RoleUUID::fromString($value);
    }
}
