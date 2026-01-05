<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Note\Application\Command\DeleteNoteCommand;
use App\Module\Note\Application\Event\NoteDeletedEvent;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use App\Module\Note\Domain\Service\NoteDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteNoteCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NoteReaderInterface $noteReaderRepository,
        private readonly NoteDeleter $noteDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.note.delete.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteNoteCommand $command): void
    {
        $this->validate($command);

        $note = $this->noteReaderRepository->getNoteByUUID($command->noteUUID);

        $this->noteDeleter->delete($note);

        $this->eventDispatcher->dispatch(new NoteDeletedEvent([
            DeleteNoteCommand::UUID => $command->noteUUID,
        ]));
    }
}
