<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeUpdatedEvent;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Service\ContractType\ContractTypeUpdater;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateContractTypeCommandHandler
{
    public function __construct(private ContractTypeUpdater $contractTypeUpdater, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(UpdateContractTypeCommand $command): void
    {
        $this->contractTypeUpdater->update($command->getContractType(), $command->getName(), $command->getDescription(), $command->getActive());
        $this->eventDispatcher->dispatch(new ContractTypeUpdatedEvent([
            ContractType::COLUMN_UUID => $command->getContractType()->getUUID(),
            ContractType::COLUMN_NAME => $command->getName(),
            ContractType::COLUMN_DESCRIPTION => $command->getDescription(),
            ContractType::COLUMN_ACTIVE => $command->getActive(),
        ]));
    }
}
