<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;

class IndustryUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return IndustryUUID::class;
    }

    protected function fromString(string $value): IndustryUUID
    {
        return IndustryUUID::fromString($value);
    }
}
