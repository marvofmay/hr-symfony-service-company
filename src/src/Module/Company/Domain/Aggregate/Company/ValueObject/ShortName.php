<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

final class ShortName
{
    private ?string $value;

    public function __construct(?string $value)
    {
        $trimmed = $value !== null ? trim($value) : null;
        $this->value = $trimmed === '' ? null : $trimmed;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}