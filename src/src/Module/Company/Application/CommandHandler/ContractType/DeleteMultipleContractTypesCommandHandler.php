<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteMultipleContractTypesCommand;
use App\Module\Company\Application\Event\ContractType\ContractTypeMultipleDeletedEvent;
use App\Module\Company\Domain\Service\ContractType\ContractTypeMultipleDeleter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteMultipleContractTypesCommandHandler
{
    public function __construct(private ContractTypeMultipleDeleter $roleMultipleDeleter, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(DeleteMultipleContractTypesCommand $command): void
    {
        $this->roleMultipleDeleter->multipleDelete($command->contractTypes);
        $this->eventDispatcher->dispatch(new ContractTypeMultipleDeletedEvent(
            $command->contractTypes->map(fn ($contractType) => $contractType->getUUID())->toArray()
        ));
    }
}
