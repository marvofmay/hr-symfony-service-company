<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Application\Event\NoteUpdatedEvent;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use App\Module\Note\Domain\Service\NoteUpdater;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateNoteCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NoteReaderInterface $noteReaderRepository,
        private readonly NoteUpdater $noteUpdater,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.note.update.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(UpdateNoteCommand $command): void
    {
        $this->validate($command);

        $note = $this->noteReaderRepository->getNoteByUUID($command->noteUUID);
        $this->noteUpdater->update($note, $command->title, $command->content, $command->priority);

        $this->eventDispatcher->dispatch(new NoteUpdatedEvent([
            UpdateNoteCommand::UUID => $command->noteUUID,
            UpdateNoteCommand::TITLE => $command->title,
            UpdateNoteCommand::CONTENT => $command->content,
            UpdateNoteCommand::PRIORITY => $command->priority,
        ]));
    }
}
