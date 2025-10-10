<?php

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeDeletedEvent;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Service\ContractType\ContractTypeDeleter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteContractTypeCommandHandler
{
    public function __construct(private ContractTypeDeleter $contractTypeDeleter, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(DeleteContractTypeCommand $command): void
    {
        $this->contractTypeDeleter->delete($command->getContractType());
        $this->eventDispatcher->dispatch(new ContractTypeDeletedEvent([
            ContractType::COLUMN_UUID => $command->getContractType()->getUUID(),
        ]));
    }
}
