<?php

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class DeleteContractTypeCommandHandler
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository,)
    {
    }

    public function __invoke(DeleteContractTypeCommand $command): void
    {
        $this->contractTypeWriterRepository->deleteContractTypeInDB($command->getContractType());
    }
}
