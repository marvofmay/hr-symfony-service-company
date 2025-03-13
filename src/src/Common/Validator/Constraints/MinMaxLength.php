<?php

declare(strict_types=1);

namespace App\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class MinMaxLength extends Constraint
{
    public function __construct(
        public int $min,
        public int $max,
        public array $message = [
            'tooShort' => 'validation.tooShort',
            'tooLong' => 'validation.tooLong',
            'domain' => 'validators',
        ],
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(['message' => $message], $groups, $payload);
    }
}
