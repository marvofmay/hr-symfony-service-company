<?php

namespace App\Module\Company\Structure\Validator\Constraints\Industry;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class UniqueIndustryName extends Constraint
{
    public string $message = 'industry.name.alreadyExists';
}
