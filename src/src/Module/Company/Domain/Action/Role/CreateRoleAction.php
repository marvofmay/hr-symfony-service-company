<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Action\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\DTO\Role\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateRoleAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

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
