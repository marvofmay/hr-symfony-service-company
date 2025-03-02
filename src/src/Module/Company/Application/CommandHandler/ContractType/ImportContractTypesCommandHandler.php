<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\ImportContractTypesCommand;
use App\Module\Company\Domain\Service\ContractType\ContractTypeMultipleCreator;

readonly class ImportContractTypesCommandHandler
{
    public function __construct(private ContractTypeMultipleCreator $contractTypeMultipleCreator,)
    {
    }

    public function __invoke(ImportContractTypesCommand $command): void
    {
        $this->contractTypeMultipleCreator->multipleCreate($command->data);
    }
}
