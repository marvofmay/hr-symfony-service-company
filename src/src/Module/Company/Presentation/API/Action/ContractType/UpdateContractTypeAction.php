<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Application\Validator\ContractType\ContractTypeValidator;
use App\Module\Company\Domain\DTO\ContractType\UpdateDTO;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateContractTypeAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly ContractTypeValidator $contractTypeValidator,
    ) {
    }

    public function execute(string $uuid, UpdateDTO $updateDTO): void
    {
        try {
            $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($uuid);
            $this->contractTypeValidator->isContractTypeNameAlreadyExists($updateDTO->name, $uuid);

            $this->commandBus->dispatch(
                new UpdateContractTypeCommand(
                    $updateDTO->name,
                    $updateDTO->description,
                    $updateDTO->active,
                    $contractType
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
