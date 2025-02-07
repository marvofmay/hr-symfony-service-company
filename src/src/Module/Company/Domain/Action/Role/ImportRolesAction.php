<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Action\Role;

use App\module\company\Application\Command\Role\ImportRolesCommand;
use App\module\company\Domain\DTO\Role\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportRolesAction
{
    public function __construct(private MessageBusInterface $commandBus) {}

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportRolesCommand($importDTO->getData()));
    }
}