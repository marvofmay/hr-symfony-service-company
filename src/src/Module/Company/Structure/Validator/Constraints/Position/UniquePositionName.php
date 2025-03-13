<?php

namespace App\Module\Company\Structure\Validator\Constraints\Position;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class UniquePositionName extends Constraint
{
    public string $message = 'position.name.alreadyExists';
}
