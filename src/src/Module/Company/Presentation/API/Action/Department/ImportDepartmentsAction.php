<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\ImportDepartmentsCommand;
use App\Module\Company\Domain\DTO\Company\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportDepartmentsAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportDepartmentsCommand($importDTO->importUUID));
    }
}
