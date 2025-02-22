<?php

namespace App\Module\Company\Structure\Validator\Constraints\Company;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class UniqueCompanyFullName extends Constraint
{
    public string $message = 'company.fullName.alreadyExists';
}
