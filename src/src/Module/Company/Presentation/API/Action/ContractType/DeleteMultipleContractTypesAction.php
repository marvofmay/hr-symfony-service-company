<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteMultipleContractTypesCommand;
use App\Module\Company\Domain\DTO\ContractType\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleContractTypesAction
{
    public function __construct(private MessageBusInterface $commandBus, private ContractTypeReaderInterface $contractTypeReaderRepository)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        try {
            $this->commandBus->dispatch(
                new DeleteMultipleContractTypesCommand(
                    $this->contractTypeReaderRepository->getContractTypesByUUID($deleteMultipleDTO->getSelectedUUID())
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
