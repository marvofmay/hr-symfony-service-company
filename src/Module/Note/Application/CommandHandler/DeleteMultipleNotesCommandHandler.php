<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Note\Application\Command\DeleteMultipleNotesCommand;
use App\Module\Note\Application\Event\NoteMultipleDeletedEvent;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use App\Module\Note\Domain\Service\NoteMultipleDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteMultipleNotesCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NoteReaderInterface $noteReaderRepository,
        private readonly NoteMultipleDeleter $noteMultipleDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.note.delete_multiple.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteMultipleNotesCommand $command): void
    {
        $this->validate($command);

        $notes = $this->noteReaderRepository->getNotesByUUIDs($command->notesUUIDs);
        $this->noteMultipleDeleter->multipleDelete($notes);

        $this->eventDispatcher->dispatch(new NoteMultipleDeletedEvent([
            DeleteMultipleNotesCommand::NOTES_UUIDS => $command->notesUUIDs,
        ]));
    }
}
