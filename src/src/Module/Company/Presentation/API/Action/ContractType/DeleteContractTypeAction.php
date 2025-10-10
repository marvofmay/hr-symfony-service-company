<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteContractTypeAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
    ) {
    }

    public function execute(string $uuid): void
    {
        try {
            $this->commandBus->dispatch(new DeleteContractTypeCommand($this->contractTypeReaderRepository->getContractTypeByUUID($uuid)));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
