<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\ImportCompaniesCommand;
use App\Module\Company\Domain\DTO\Company\ImportDTO;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportCompaniesAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportDTO $importDTO): void
    {
        try {
            $this->commandBus->dispatch(new ImportCompaniesCommand($importDTO->importUUID));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
