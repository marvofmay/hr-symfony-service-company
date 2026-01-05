<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

interface ContractTypeRestorerInterface
{
    public function restore(ContractType $contractType): void;
}
