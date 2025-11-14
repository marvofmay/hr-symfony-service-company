<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Note\Application\Query\GetNotePDFQuery;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

final class NotePDFController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'event.bus')] private readonly MessageBusInterface $eventBus,
        #[Autowire(service: 'query.bus')] private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/employees/notes/{uuid}/pdf', name: 'api.employees.note.pdf', methods: ['GET'])]
    public function pdf(string $uuid): Response
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::PDF,
                AccessEnum::NOTE,
                $this->messageService->get('accessDenied')
            );

            $pdf = $this->dispatchQuery($uuid);

            return $this->successResponse($pdf);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchQuery(string $noteUUID): string
    {
        try {
            $handledStamp = $this->queryBus->dispatch(new GetNotePDFQuery($noteUUID));

            return $handledStamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(string $pdf): Response
    {
        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="notes.pdf"',
        ]);
    }

    private function errorResponse(\Throwable $exception): JsonResponse
    {
        $message = sprintf(
            '%s. %s',
            $this->messageService->get('note.pdf.error', [], 'notes'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::EVENT_LOG));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
