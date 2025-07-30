<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Employee\ValueObject;

use App\Common\Shared\Utils\PESELValidator;

final readonly class PESEL
{
    private string $value;

    public function __construct(string $pesel)
    {
        $error = PESELValidator::validate($pesel);

        if ($error !== null) {
            throw new \InvalidArgumentException(sprintf('Invalid PESEL: %s', $error));
        }

        $this->value = $pesel;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}