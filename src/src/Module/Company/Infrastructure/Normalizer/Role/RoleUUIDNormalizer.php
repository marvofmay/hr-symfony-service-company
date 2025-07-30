<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\Role;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\RoleUUID;
use App\Module\Company\Infrastructure\Normalizer\AbstractUUIDNormalizer;

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