<?php

namespace App\Structure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[\Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final class UniqueRoleName extends Constraint
{
    public string $message = 'Role with the name "{{ name }}" already exists.';

    public function __construct(
        array $options = [],
        ?string $message = null
    ) {
        parent::__construct($options);

        if ($message !== null) {
            $this->message = $message;
        }
    }
}
