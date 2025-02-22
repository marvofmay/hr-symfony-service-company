<?php

namespace App\Module\Company\Structure\Validator\Constraints\Department;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class UniqueDepartmentName extends Constraint
{
    public string $message = 'department.name.alreadyExists';
}
