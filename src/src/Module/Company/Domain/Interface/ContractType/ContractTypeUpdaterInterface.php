<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;

interface ContractTypeUpdaterInterface
{
    public function update(UpdateContractTypeCommand $command): void;
}