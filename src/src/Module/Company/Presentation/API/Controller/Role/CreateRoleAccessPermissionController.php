<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Role\CreateAccessPermissionDTO;
use App\Module\Company\Presentation\API\Action\Role\CreateRoleAccessPermissionAction;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateRoleAccessPermissionController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/roles/{uuid}/accesses/permissions', name: 'api.roles.accesses.permissions.create', methods: ['POST'])]
    public function create(string $uuid, #[MapRequestPayload] CreateAccessPermissionDTO $createAccessPermissionDTO, CreateRoleAccessPermissionAction $createRoleAccessPermissionAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::ASSIGN_PERMISSION_TO_ACCESS_ROLE, AccessEnum::PERMISSION)) {
                throw new \Exception($this->messageService->get('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $createRoleAccessPermissionAction->execute($uuid, $createAccessPermissionDTO);

            return new JsonResponse(
                ['message' => $this->messageService->get('role.add.permission.success', [], 'roles')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('role.add.permission.error', [], 'roles'), $error->getMessage());
            $this->eventBus->dispatch(new LogFileEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
