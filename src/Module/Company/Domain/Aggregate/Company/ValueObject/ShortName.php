<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

final class ShortName extends CompanyNameAbstract
{
    public function __construct(?string $value)
    {
        $trimmed = null !== $value ? trim($value) : null;
        parent::__construct('' === $trimmed ? null : $trimmed);
    }

    public static function fromString(string $value): static
    {
        return new self($value);
    }
}
