<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\Action\Role;

use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\Company\Domain\DTO\Role\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportRolesAction
{
    public function __construct(private MessageBusInterface $commandBus) {}

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportRolesCommand($importDTO->getData()));
    }
}