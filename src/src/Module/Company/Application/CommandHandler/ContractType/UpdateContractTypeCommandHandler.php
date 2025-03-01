<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class UpdateContractTypeCommandHandler
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository,)
    {
    }

    public function __invoke(UpdateContractTypeCommand $command): void
    {
        $contractType = $command->getContractType();
        $contractType->setName($command->getName());
        $contractType->setDescription($command->getDescription());
        $contractType->setUpdatedAt(new \DateTime());

        $this->contractTypeWriterRepository->updateContractTypeInDB($contractType);
    }
}
