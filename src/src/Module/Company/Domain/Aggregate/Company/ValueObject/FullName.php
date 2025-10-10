<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

final class FullName extends CompanyNameAbstract
{
    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ('' === $trimmed) {
            throw new \InvalidArgumentException('FullName is required.');
        }

        if (mb_strlen($trimmed) < 3) {
            throw new \InvalidArgumentException('FullName must be at least 3 characters.');
        }

        if (mb_strlen($trimmed) > 500) {
            throw new \InvalidArgumentException('FullName must be at most 500 characters.');
        }

        parent::__construct($trimmed);
    }

    public static function fromString(string $value): static
    {
        return new self($value);
    }
}
