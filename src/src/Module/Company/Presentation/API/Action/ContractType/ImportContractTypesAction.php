<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\contractType;

use App\Module\Company\Application\Command\contractType\ImportContractTypesCommand;
use App\Module\Company\Domain\DTO\contractType\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportContractTypesAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportContractTypesCommand($importDTO->getData()));
    }
}
