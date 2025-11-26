<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Employee\ValueObject;

class NameAbstract
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->assertNotEmpty($value);
        $this->assertLength($value);
        $this->value = $value;
    }

    private function assertNotEmpty(string $value): void
    {
        if ('' === trim($value)) {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
    }

    private function assertLength(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < 3 || $length > 50) {
            throw new \InvalidArgumentException(sprintf('Name must be between 3 and 50 characters, "%s" given (%d chars).', $value, $length));
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
