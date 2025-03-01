<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class CreateContractTypeCommandHandler
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function __invoke(CreateContractTypeCommand $command): void
    {
        $position = new ContractType();
        $position->setName($command->name);
        $position->setDescription($command->description);
        $position->setActive($command->active);

        $this->contractTypeWriterRepository->saveContractTypeInDB($position);
    }
}
