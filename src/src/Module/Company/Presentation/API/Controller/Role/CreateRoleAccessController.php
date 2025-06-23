<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Role\CreateAccessDTO;
use App\Module\Company\Presentation\API\Action\Role\CreateRoleAccessAction;
use App\Module\System\Application\Event\LogEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CreateRoleAccessController extends AbstractController
{
    public function __construct(private readonly EventDispatcherInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/roles/{uuid}/accesses', name: 'api.roles.accesses.create', methods: ['POST'])]
    public function create(string $uuid, #[MapRequestPayload] CreateAccessDTO $createAccessDTO, CreateRoleAccessAction $createRoleAccessAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::ASSIGN_ACCESS_TO_ROLE, AccessEnum::ACCESS)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $createRoleAccessAction->execute($uuid, $createAccessDTO);

            return new JsonResponse(['message' => $this->messageService->get('role.add.access.success', [], 'roles')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('role.add.access.error', [], 'roles'), $error->getMessage());
            $this->eventBus->dispatch(new LogEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
