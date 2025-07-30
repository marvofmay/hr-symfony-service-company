<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\Aggregate\Employee\ValueObject;

class EmploymentTo extends EmploymentDateAbstract
{
    public function __construct(?string $dateString, EmploymentFrom $from)
    {
        if ($dateString === null) {
            $this->date = $from->toDateTime();

            return;
        }

        parent::__construct($dateString);

        if ($this->date <= $from->toDateTime()) {
            throw new \InvalidArgumentException("EmploymentTo must be after EmploymentFrom.");
        }
    }
}