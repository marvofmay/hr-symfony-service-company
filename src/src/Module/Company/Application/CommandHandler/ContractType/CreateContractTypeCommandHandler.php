<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Domain\Service\ContractType\ContractTypeCreator;

readonly class CreateContractTypeCommandHandler
{
    public function __construct(private ContractTypeCreator $contractTypeCreator,)
    {
    }

    public function __invoke(CreateContractTypeCommand $command): void
    {
        $this->contractTypeCreator->create($command->name, $command->description, $command->active);
    }
}
