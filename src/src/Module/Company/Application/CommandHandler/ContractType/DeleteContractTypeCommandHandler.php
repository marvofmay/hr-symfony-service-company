<?php

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeDeletedEvent;
use App\Module\Company\Domain\Service\ContractType\ContractTypeDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteContractTypeCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly ContractTypeDeleter $contractTypeDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.contract_type.delete.validator')] protected iterable $validators,
    )
    {
    }

    public function __invoke(DeleteContractTypeCommand $command): void
    {
        $this->validate($command);

        $this->contractTypeDeleter->delete($command);

        $this->eventDispatcher->dispatch(new ContractTypeDeletedEvent([
            DeleteContractTypeCommand::CONTRACT_TYPE_UUID => $command->contractTypeUUID,
        ]));
    }
}
