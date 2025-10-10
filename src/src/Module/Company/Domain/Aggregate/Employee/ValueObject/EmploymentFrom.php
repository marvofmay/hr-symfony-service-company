<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Employee\ValueObject;

class EmploymentFrom extends EmploymentDateAbstract
{
    public static function fromString(string $dateString): self
    {
        return new self($dateString);
    }
}
