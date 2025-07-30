<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\Employee;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EmploymentDateRange extends Constraint
{
    public array $message;
    public string $domain;

    public function __construct(array $message = [], string $domain = 'messages', ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
        $this->message = $message;
        $this->domain = $domain;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}