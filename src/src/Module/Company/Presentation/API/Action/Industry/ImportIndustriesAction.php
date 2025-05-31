<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\ImportIndustriesCommand;
use App\Module\Company\Domain\DTO\Industry\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportIndustriesAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportIndustriesCommand($importDTO->importUUID));
    }
}
