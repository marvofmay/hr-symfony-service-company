<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

abstract class CompanyNameAbstract
{
    protected ?string $value;

    protected function __construct(?string $value)
    {
        $this->value = null !== $value ? trim($value) : null;
    }

    abstract public static function fromString(?string $value): static;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
