<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Note\Application\Query\GetNotesPDFQuery;
use App\Module\Note\Domain\DTO\NotesPDFQueryDTO;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class NotesPDFController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'query.bus')] private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    )
    {
    }

    #[Route('/api/employees/notes/pdf', name: 'api.employees.notes.pdf', methods: ['GET'])]
    public function __invoke(#[MapQueryString] NotesPDFQueryDTO $dto): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::PDF, AccessEnum::NOTE, $this->messageService->get('accessDenied'));

        try {
            $pdf = $this->queryBus->dispatch(new GetNotesPDFQuery($dto->uuids))->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new Response(
            $pdf,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="notes.pdf"',
            ]
        );
    }
}