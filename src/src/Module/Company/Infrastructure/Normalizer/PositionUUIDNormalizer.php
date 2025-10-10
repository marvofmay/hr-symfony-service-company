<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PositionUUID;

class PositionUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return PositionUUID::class;
    }

    protected function fromString(string $value): PositionUUID
    {
        return PositionUUID::fromString($value);
    }
}
