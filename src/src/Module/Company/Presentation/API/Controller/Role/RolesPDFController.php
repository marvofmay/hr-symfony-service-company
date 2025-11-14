<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Query\Role\GetRolesPDFQuery;
use App\Module\Company\Domain\DTO\Role\RolesPDFQueryDTO;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

final class RolesPDFController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'event.bus')] private readonly MessageBusInterface $eventBus,
        #[Autowire(service: 'query.bus')] private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/roles/pdf', name: 'api.roles.pdf', methods: ['GET'])]
    public function pdf(#[MapQueryString] RolesPDFQueryDTO $rolesPDFQueryDTO): Response
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::PDF,
                AccessEnum::ROLE,
                $this->messageService->get('accessDenied')
            );

            $pdf = $this->dispatchQuery($rolesPDFQueryDTO);

            return $this->successResponse($pdf);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchQuery(RolesPDFQueryDTO $rolesPDFQueryDTO): string
    {
        try {
            $handledStamp = $this->queryBus->dispatch(new GetRolesPDFQuery($rolesPDFQueryDTO->uuids));

            return $handledStamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(string $pdf): Response
    {
        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="roles.pdf"',
        ]);
    }

    private function errorResponse(\Throwable $exception): JsonResponse
    {
        $message = sprintf(
            '%s %s',
            $this->messageService->get('role.pdf.error', [], 'roles'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::EVENT_LOG));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
