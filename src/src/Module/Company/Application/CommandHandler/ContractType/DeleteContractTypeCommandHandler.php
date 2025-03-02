<?php

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;
use App\Module\Company\Domain\Service\ContractType\ContractTypeDeleter;

readonly class DeleteContractTypeCommandHandler
{
    public function __construct(private ContractTypeDeleter $contractTypeDeleter,)
    {
    }

    public function __invoke(DeleteContractTypeCommand $command): void
    {
        $this->contractTypeDeleter->delete($command->getContractType());
    }
}
