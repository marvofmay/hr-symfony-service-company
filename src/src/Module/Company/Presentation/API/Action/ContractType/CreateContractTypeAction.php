<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Application\Validator\ContractType\ContractTypeValidator;
use App\Module\Company\Domain\DTO\ContractType\CreateDTO;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateContractTypeAction
{
    public function __construct(private MessageBusInterface $commandBus, private ContractTypeValidator $contractTypeValidator)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        try {
            $this->contractTypeValidator->isContractTypeNameAlreadyExists($createDTO->name);
            $this->commandBus->dispatch(
                new CreateContractTypeCommand(
                    $createDTO->name,
                    $createDTO->description,
                    $createDTO->active
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
