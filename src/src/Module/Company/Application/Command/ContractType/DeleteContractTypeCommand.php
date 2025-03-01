<?php

namespace App\Module\Company\Application\Command\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

readonly class DeleteContractTypeCommand
{
    public function __construct(private ContractType $role)
    {
    }

    public function getContractType(): ContractType
    {
        return $this->role;
    }
}
