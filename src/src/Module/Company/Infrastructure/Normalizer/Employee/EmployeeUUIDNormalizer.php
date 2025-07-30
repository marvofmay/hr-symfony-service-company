<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\Employee;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Infrastructure\Normalizer\AbstractUUIDNormalizer;

class EmployeeUUIDNormalizer extends AbstractUUIDNormalizer
{
    protected function getSupportedClass(): string
    {
        return EmployeeUUID::class;
    }

    protected function fromString(string $value): EmployeeUUID
    {
        return EmployeeUUID::fromString($value);
    }
}