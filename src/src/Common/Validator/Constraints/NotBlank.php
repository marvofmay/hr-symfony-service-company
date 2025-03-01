<?php

declare(strict_types=1);

namespace App\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class NotBlank extends Constraint
{
    public function __construct(
        public array $message = [
            'text' => 'validation.notBlank',
            'domain' => 'validators',
        ],
        public ?array $groups = null,
        public mixed $payload = null,
    ) {
        parent::__construct(['message' => $message], $groups, $payload);
    }
}
