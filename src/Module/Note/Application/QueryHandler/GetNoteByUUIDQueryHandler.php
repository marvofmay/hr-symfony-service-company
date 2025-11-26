<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Note\Application\Event\NoteViewedEvent;
use App\Module\Note\Application\Query\GetNoteByUUIDQuery;
use App\Module\Note\Application\Transformer\NoteDataTransformer;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetNoteByUUIDQueryHandler
{
    public function __construct(
        private NoteReaderInterface $noteReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.note.query.get.validator')] protected iterable $validators,
    )
    {
    }

    public function __invoke(GetNoteByUUIDQuery $query): array
    {
        $this->validate($query);

        $note = $this->noteReaderRepository->getNoteByUUID($query->noteUUID);
        $transformer = new NoteDataTransformer();

        $this->eventDispatcher->dispatch(new NoteViewedEvent([GetNoteByUUIDQuery::NOTE_UUID => $query->noteUUID]));

        return $transformer->transformToArray($note);
    }

    private function validate(QueryInterface $query): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($query)) {
                $validator->validate($query);
            }
        }
    }
}
