<?php

namespace App\Module\Company\Structure\Validator\Constraints\Employee;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class UniqueEmployeeEmail extends Constraint
{
    public string $message = 'employee.email.alreadyExists';
}
