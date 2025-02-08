<?php

declare(strict_types = 1);

namespace App\Module\Note\Domain\Action;

use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\DTO\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateNoteAction
{
    public function __construct(private MessageBusInterface $commandBus) {}

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateNoteCommand(
                $createDTO->getTitle(),
                $createDTO->getContent(),
                $createDTO->getPriority(),
            )
        );
    }
}