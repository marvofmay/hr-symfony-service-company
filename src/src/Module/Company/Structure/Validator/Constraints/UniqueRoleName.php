<?php

namespace App\Module\Company\Structure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class UniqueRoleName extends Constraint
{
    public string $message = 'role.name.alreadyExists';
}