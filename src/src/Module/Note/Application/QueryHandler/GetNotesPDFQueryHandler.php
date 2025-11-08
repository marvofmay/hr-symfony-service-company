<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Common\Application\QueryHandler\GetQueryHandlerAbstract;
use App\Module\Note\Application\Event\NotesPDFCreatedEvent;
use App\Module\Note\Application\Query\GetNotesPDFQuery;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use App\Module\System\Domain\Service\Pdf\PDFService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetNotesPDFQueryHandler extends GetQueryHandlerAbstract
{
    public function __construct(
        private readonly PDFService $pdfService,
        private readonly NoteReaderInterface $noteReaderRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Security $security,
        #[AutowireIterator(tag: 'app.notes.pdf.query.get.validator')] protected iterable $validators,
    ) {}

    public function __invoke(GetNotesPDFQuery $query): string
    {
        $this->validate($query);

        $employee = $this->security->getUser()->getEmployee();

        $notesUUIDs = $query->notesUUIDs;
        $notes = $this->noteReaderRepository->getNotesByUUIDsAndEmployee($notesUUIDs, $employee);

        $this->eventDispatcher->dispatch(new NotesPDFCreatedEvent([
            GetNotesPDFQuery::NOTES_UUIDS => $notesUUIDs,
        ]));

        return $this->pdfService->streamPdf('pdfs/note/notes_list.html.twig', ['notes' => $notes, 'baseUrl' => $_ENV['APP_URL']]);
    }
}