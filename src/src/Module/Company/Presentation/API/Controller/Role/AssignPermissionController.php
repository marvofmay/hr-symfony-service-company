<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Command\Role\AssignPermissionsCommand;
use App\Module\Company\Domain\DTO\Role\AssignPermissionsDTO;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AssignPermissionController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'event.bus')] private readonly MessageBusInterface $eventBus,
        #[Autowire(service: 'command.bus')] private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService
    )
    {
    }

    #[Route('/api/roles/{uuid}/accesses/permissions', name: 'api.roles.accesses.permissions.create', methods: ['POST'])]
    public function create(string $uuid, #[MapRequestPayload] AssignPermissionsDTO $assignPermissionsDTO): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::ASSIGN_PERMISSION_TO_ACCESS_ROLE,
                AccessEnum::ROLE,
                $this->messageService->get('accessDenied')
            );

            $this->dispatchCommand($uuid, $assignPermissionsDTO);

            return $this->successResponse();
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchCommand(string $roleUUID, AssignPermissionsDTO $assignPermissionsDTO): void
    {
        try {
            $this->commandBus->dispatch(new AssignPermissionsCommand($roleUUID, $assignPermissionsDTO->accesses));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(): JsonResponse
    {
        return new JsonResponse(
            ['message' => $this->messageService->get('role.assign.permission.success', [], 'roles')],
            Response::HTTP_CREATED
        );
    }

    private function errorResponse(\Throwable $exception): JsonResponse
    {
        $message = sprintf(
            '%s %s',
            $this->messageService->get('role.assign.permission.error', [], 'roles'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::EVENT_LOG));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
