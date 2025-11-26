<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\ContractType\DeleteMultipleContractTypesCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Service\ContractType\ContractTypeMultipleDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteMultipleContractTypesCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly ContractTypeReaderInterface $contractTypeReaderInterface,
        private readonly ContractTypeMultipleDeleter $roleMultipleDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.contract_type.delete_multiple.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteMultipleContractTypesCommand $command): void
    {
        $this->validate($command);

        $contractTypes = $this->contractTypeReaderInterface->getContractTypesByUUIDs($command->contractTypesUUIDs);
        $this->roleMultipleDeleter->multipleDelete($contractTypes);

        $this->eventDispatcher->dispatch(new ContractTypeMultipleDeletedEvent([
            DeleteMultipleContractTypesCommand::CONTRACT_TYPES_UUIDS => $command->contractTypesUUIDs,
        ]));
    }
}
