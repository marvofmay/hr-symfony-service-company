<?php

declare(strict_types=1);

namespace App\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class MinMaxLength extends Constraint
{
    public function __construct(
        public readonly int $min,
        public readonly int $max,
        public readonly array $message = [],
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy(): string
    {
        return MinMaxLengthValidator::class;
    }
}
