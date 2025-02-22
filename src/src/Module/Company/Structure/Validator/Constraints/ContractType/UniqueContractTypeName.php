<?php

namespace App\Module\Company\Structure\Validator\Constraints\ContractType;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class UniqueContractTypeName extends Constraint
{
    public string $message = 'contractType.name.alreadyExists';
}
