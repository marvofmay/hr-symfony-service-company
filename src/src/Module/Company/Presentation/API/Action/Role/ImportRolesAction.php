<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\Company\Domain\DTO\Role\ImportDTO;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ImportRolesAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        try {
            $this->commandBus->dispatch(new ImportRolesCommand($importDTO->importUUID));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
