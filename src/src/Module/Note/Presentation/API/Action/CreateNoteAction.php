<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Action;

use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\DTO\CreateDTO;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateNoteAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        try {
            $this->commandBus->dispatch(
                new CreateNoteCommand(
                    $createDTO->getEmployeeUUID(),
                    $createDTO->getTitle(),
                    $createDTO->getContent(),
                    $createDTO->getPriority(),
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
