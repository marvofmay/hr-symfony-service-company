<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Action\Role;

use App\module\company\Application\Command\Role\CreateRoleCommand;
use App\module\company\Domain\DTO\Role\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateRoleAction
{
    public function __construct(private MessageBusInterface $commandBus) {}

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateRoleCommand(
                $createDTO->getName(),
                $createDTO->getDescription()
            )
        );
    }
}