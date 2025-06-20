<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteMultipleContractTypesCommand;
use App\Module\Company\Domain\Service\ContractType\ContractTypeMultipleDeleter;

readonly class DeleteMultipleContractTypesCommandHandler
{
    public function __construct(private ContractTypeMultipleDeleter $roleMultipleDeleter)
    {
    }

    public function __invoke(DeleteMultipleContractTypesCommand $command): void
    {
        $this->roleMultipleDeleter->multipleDelete($command->contractTypes);
    }
}
