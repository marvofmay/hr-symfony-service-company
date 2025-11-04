<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;

interface ContractTypeCreatorInterface
{
    public function create(CreateContractTypeCommand $command): void;
}