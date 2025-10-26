<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Employee\ValueObject;

class EmploymentDateAbstract
{
    protected ?\DateTimeImmutable $date;

    public function __construct(string $dateString)
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $dateString);

        if (!$date || $date->format('Y-m-d') !== $dateString) {
            throw new \InvalidArgumentException('Invalid date format, expected Y-m-d.');
        }

        $this->date = $date;
    }

    public function toDateTime(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function __toString(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function getValue(): string
    {
        return $this->date->format('Y-m-d');
    }
}
