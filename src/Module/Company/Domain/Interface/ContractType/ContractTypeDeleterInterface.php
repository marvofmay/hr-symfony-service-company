<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

interface ContractTypeDeleterInterface
{
    public function delete(ContractType $contractType): void;
}
