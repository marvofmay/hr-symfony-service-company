<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Note\Application\Query\GetNotePDFQuery;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class NotePDFController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/employees/notes/{uuid}/pdf', name: 'api.employees.note.pdf', methods: ['GET'])]
    public function __invoke(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::PDF, AccessEnum::NOTE, $this->messageService->get('accessDenied'));

        try {
            $pdf = $this->queryBus->dispatch(new GetNotePDFQuery($uuid))->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new Response($pdf, Response::HTTP_OK, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="notes.pdf"',]);
    }
}
