<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Action\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Domain\DTO\ContractType\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateContractTypeAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateContractTypeCommand(
                $createDTO->getName(),
                $createDTO->getDescription(),
                $createDTO->getActive()
            )
        );
    }
}
