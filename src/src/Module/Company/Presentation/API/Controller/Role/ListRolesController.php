<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Role\RolesQueryDTO;
use App\Module\Company\Presentation\API\Action\Role\AskRolesAction;
use App\Module\System\Application\Event\LogEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ListRolesController extends AbstractController
{
    public function __construct(private readonly EventDispatcherInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/roles', name: 'api.roles.list', methods: ['GET'])]
    public function list(#[MapQueryString] RolesQueryDTO $queryDTO, AskRolesAction $askRolesAction): Response
    {
        try {
            if (!$this->isGranted(PermissionEnum::LIST, AccessEnum::ROLE)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            return new JsonResponse(['data' => $askRolesAction->ask($queryDTO)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('role.list.error', [], 'roles'), $error->getMessage());
            $this->eventBus->dispatch(new LogEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
