<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\ImportPositionsCommand;
use App\Module\Company\Domain\DTO\Position\ImportDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportPositionsAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        $this->commandBus->dispatch(new ImportPositionsCommand($importDTO->getData()));
    }
}
