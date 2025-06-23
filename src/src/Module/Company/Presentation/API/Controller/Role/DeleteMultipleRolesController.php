<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Role\DeleteMultipleDTO;
use App\Module\Company\Presentation\API\Action\Role\DeleteMultipleRolesAction;
use App\Module\System\Application\Event\LogEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DeleteMultipleRolesController extends AbstractController
{
    public function __construct(private readonly EventDispatcherInterface $eventBus, private readonly MessageService $messageService)
    {
    }


    #[Route('/api/roles/multiple', name: 'api.roles.delete_multiple', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO, DeleteMultipleRolesAction $deleteMultipleRolesAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::DELETE, AccessEnum::ROLE)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $deleteMultipleRolesAction->execute($deleteMultipleDTO);

            return new JsonResponse(
                ['message' => $this->messageService->get('role.delete.multiple.success', [], 'roles')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('role.delete.multiple.error', [], 'roles'), $error->getMessage());
            $this->eventBus->dispatch(new LogEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
