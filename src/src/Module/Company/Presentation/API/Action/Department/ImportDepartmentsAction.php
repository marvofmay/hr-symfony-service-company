<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\ImportDepartmentsCommand;
use App\Module\Company\Domain\DTO\Department\ImportDTO;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ImportDepartmentsAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        try {
            $this->commandBus->dispatch(new ImportDepartmentsCommand($importDTO->importUUID));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
