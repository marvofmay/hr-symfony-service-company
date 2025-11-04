<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;

interface ContractTypeDeleterInterface
{
    public function delete(DeleteContractTypeCommand $command): void;
}