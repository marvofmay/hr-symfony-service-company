<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class DeleteIndustryAction
{
    public function __construct(private MessageBusInterface $commandBus, private IndustryReaderInterface $roleReaderRepository)
    {
    }

    public function execute(string $uuid): void
    {
        try {
            $this->commandBus->dispatch(new DeleteIndustryCommand($this->roleReaderRepository->getIndustryByUUID($uuid)));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
