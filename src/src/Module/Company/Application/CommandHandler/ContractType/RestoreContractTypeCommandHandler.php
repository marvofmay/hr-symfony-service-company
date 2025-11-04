<?php

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\ContractType\RestoreContractTypeCommand;
use App\Module\Company\Application\Event\Industry\IndustryRestoredEvent;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Service\ContractType\ContractTypeRestorer;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RestoreContractTypeCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly ContractTypeRestorer $contractTypeRestorer,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.contract_type.restore.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(RestoreContractTypeCommand $command): void
    {
        $this->validate($command);

        $contractType = $this->contractTypeReaderRepository->getDeletedContractTypeByUUID($command->contractTypeUUID);
        $this->contractTypeRestorer->restore($contractType);
        $this->eventDispatcher->dispatch(new IndustryRestoredEvent([
            RestoreContractTypeCommand::CONTRACT_TYPE_UUID => $command->contractTypeUUID,
        ]));
    }
}
