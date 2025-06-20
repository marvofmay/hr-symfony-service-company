<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Domain\DTO\ContractType\UpdateDTO;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateContractTypeAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private readonly ContractTypeReaderInterface $contractTypeReaderRepository)
    {
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdateContractTypeCommand(
                $updateDTO->getUUID(),
                $updateDTO->getName(),
                $updateDTO->getDescription(),
                $updateDTO->getActive(),
                $this->contractTypeReaderRepository->getContractTypeByUUID($updateDTO->getUUID())
            )
        );
    }
}
