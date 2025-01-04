<?php

declare(strict_types = 1);

namespace App\Domain\Action\Role;

use App\Application\Command\Role\CreateRoleCommand;
use App\Domain\DTO\Role\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateRoleAction
{
    public function __construct(private readonly MessageBusInterface $commandBus) {}

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