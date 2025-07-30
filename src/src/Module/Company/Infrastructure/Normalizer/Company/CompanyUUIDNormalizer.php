<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\Company;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Infrastructure\Normalizer\AbstractUUIDNormalizer;

class CompanyUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return CompanyUUID::class;
    }

    protected function fromString(string $value): CompanyUUID
    {
        return CompanyUUID::fromString($value);
    }
}