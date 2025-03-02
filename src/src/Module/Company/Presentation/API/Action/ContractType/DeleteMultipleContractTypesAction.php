<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteMultipleContractTypesCommand;
use App\Module\Company\Domain\DTO\ContractType\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleContractTypesAction
{
    public function __construct(private MessageBusInterface $commandBus, private ContractTypeReaderInterface $roleReaderRepository)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultipleContractTypesCommand(
                $this->roleReaderRepository->getContractTypesByUUID($deleteMultipleDTO->getSelectedUUID())
            )
        );
    }
}
