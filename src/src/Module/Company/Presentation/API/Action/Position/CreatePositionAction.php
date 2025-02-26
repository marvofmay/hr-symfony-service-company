<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\DTO\Position\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreatePositionAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreatePositionCommand(
                $createDTO->getName(),
                $createDTO->getDescription(),
                $createDTO->getActive()
            )
        );
    }
}
