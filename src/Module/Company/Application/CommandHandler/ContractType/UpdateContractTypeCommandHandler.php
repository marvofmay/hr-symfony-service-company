<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeUpdatedEvent;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Service\ContractType\ContractTypeUpdater;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateContractTypeCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly ContractTypeUpdater $contractTypeUpdater,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.contract_type.update.validator')] protected iterable $validators,
    )
    {
    }

    public function __invoke(UpdateContractTypeCommand $command): void
    {
        $this->validate($command);

        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($command->contractTypeUUID);
        $this->contractTypeUpdater->update(
            contractType: $contractType,
            name: $command->name,
            description: $command->description,
            active: $command->active
        );

        $this->eventDispatcher->dispatch(new ContractTypeUpdatedEvent([
            UpdateContractTypeCommand::CONTRACT_TYPE_UUID => $command->contractTypeUUID,
            UpdateContractTypeCommand::CONTRACT_TYPE_NAME => $command->name,
            UpdateContractTypeCommand::CONTRACT_TYPE_DESCRIPTION => $command->description,
            UpdateContractTypeCommand::CONTRACT_TYPE_ACTIVE => $command->active,
        ]));
    }
}
