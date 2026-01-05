<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Application\Event\NoteCreatedEvent;
use App\Module\Note\Domain\Service\NoteCreator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateNoteCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NoteCreator $noteCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Security $security
    ) {
    }

    public function __invoke(CreateNoteCommand $command): void
    {
        $user = $this->security->getUser();
        $this->noteCreator->create(
            user: $user,
            title: $command->title,
            content: $command->content,
            priority: $command->priority,
        );

        $this->eventDispatcher->dispatch(new NoteCreatedEvent([
            CreateNoteCommand::TITLE => $command->title,
            CreateNoteCommand::CONTENT => $command->content,
            CreateNoteCommand::PRIORITY => $command->priority,
        ]));
    }
}
