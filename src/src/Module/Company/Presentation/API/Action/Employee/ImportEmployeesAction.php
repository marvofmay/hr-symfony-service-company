<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\ImportEmployeesCommand;
use App\Module\Company\Domain\DTO\Employee\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportEmployeesAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportEmployeesCommand($importDTO->uploadFilePath, $importDTO->fileName));
    }
}
