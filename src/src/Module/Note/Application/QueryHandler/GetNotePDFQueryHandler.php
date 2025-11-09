<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Common\Application\QueryHandler\GetQueryHandlerAbstract;
use App\Module\Note\Application\Event\NotePDFCreatedEvent;
use App\Module\Note\Application\Query\GetNotePDFQuery;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use App\Module\System\Domain\Service\Pdf\PDFService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetNotePDFQueryHandler extends GetQueryHandlerAbstract
{
    public function __construct(
        private readonly PDFService $pdfService,
        private readonly NoteReaderInterface $noteReaderRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Security $security,
        #[AutowireIterator(tag: 'app.note.pdf.query.get.validator')] protected iterable $validators,
    ) {}

    public function __invoke(GetNotePDFQuery $query): string
    {
        $this->validate($query);

        $employee = $this->security->getUser()->getEmployee();

        $noteUUID = $query->noteUUID;
        $note = $this->noteReaderRepository->getNoteByUUIDAndEmployee($noteUUID, $employee);

        $this->eventDispatcher->dispatch(new NotePDFCreatedEvent([
            GetNotePDFQuery::NOTE_UUID => $noteUUID,
        ]));

        return $this->pdfService->streamPdf('pdfs/note/note.html.twig', ['note' => $note, 'baseUrl' => $_ENV['APP_URL']]);
    }
}