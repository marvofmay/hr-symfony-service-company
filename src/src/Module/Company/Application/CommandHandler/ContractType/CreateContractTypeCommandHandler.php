<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeCreatedEvent;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Service\ContractType\ContractTypeCreator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateContractTypeCommandHandler
{
    public function __construct(private ContractTypeCreator $contractTypeCreator, private EventDispatcherInterface $eventDispatcher,)
    {
    }

    public function __invoke(CreateContractTypeCommand $command): void
    {
        $this->contractTypeCreator->create($command->name, $command->description, $command->active);
        $this->eventDispatcher->dispatch(new ContractTypeCreatedEvent([
            ContractType::COLUMN_NAME        => $command->name,
            ContractType::COLUMN_DESCRIPTION => $command->description,
            ContractType::COLUMN_ACTIVE      => $command->active,
        ]));
    }
}
