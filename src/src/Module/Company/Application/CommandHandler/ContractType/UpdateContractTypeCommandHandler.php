<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Domain\Service\ContractType\ContractTypeUpdater;

readonly class UpdateContractTypeCommandHandler
{
    public function __construct(private ContractTypeUpdater $contractTypeUpdater)
    {
    }

    public function __invoke(UpdateContractTypeCommand $command): void
    {
        $this->contractTypeUpdater->update($command->getContractType(), $command->getName(), $command->getDescription(), $command->getActive());
    }
}
