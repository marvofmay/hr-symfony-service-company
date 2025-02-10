<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class MinMaxLength extends Constraint
{
    public int $min;
    public int $max;
    public array $message = [
        'tooShort' => 'validation.tooShort',
        'tooLong' => 'validation.tooLong',
        'domain' => 'validators'
    ];

    public function __construct(
        int $min,
        int $max,
        array $message
    ) {
        $this->min = $min;
        $this->max = $max;

        parent::__construct(['message' => $message ?? $this->message]);
    }
}
