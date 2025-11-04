<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeCreatedEvent;
use App\Module\Company\Domain\Service\ContractType\ContractTypeCreator;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateContractTypeCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly ContractTypeCreator $contractTypeCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.contract_type.create.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(CreateContractTypeCommand $command): void
    {
        $this->validate($command);

        $this->contractTypeCreator->create(
            name: $command->name,
            description: $command->description,
            active: $command->active
        );

        $this->eventDispatcher->dispatch(new ContractTypeCreatedEvent([
            CreateContractTypeCommand::CONTRACT_TYPE_NAME        => $command->name,
            CreateContractTypeCommand::CONTRACT_TYPE_DESCRIPTION => $command->description,
            CreateContractTypeCommand::CONTRACT_TYPE_ACTIVE      => $command->active,
        ]));
    }
}
