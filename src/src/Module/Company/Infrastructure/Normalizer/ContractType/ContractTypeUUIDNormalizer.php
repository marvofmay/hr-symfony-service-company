<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\ContractType;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\ContractTypeUUID;
use App\Module\Company\Infrastructure\Normalizer\AbstractUUIDNormalizer;

class ContractTypeUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return ContractTypeUUID::class;
    }

    protected function fromString(string $value): ContractTypeUUID
    {
        return ContractTypeUUID::fromString($value);
    }
}