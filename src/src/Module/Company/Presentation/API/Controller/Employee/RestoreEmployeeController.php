<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Presentation\API\Action\Employee\RestoreEmployeeAction;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class RestoreEmployeeController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/employees/{uuid}/restore', name: 'api.employees.restore', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['PATCH'])]
    public function restore(string $uuid, RestoreEmployeeAction $restoreEmployeeAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::RESTORE, AccessEnum::EMPLOYEE)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $restoreEmployeeAction->execute($uuid);

            return new JsonResponse(['message' => $this->messageService->get('employee.restore.success', [], 'employees')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('employee.restore.error', [], 'employees'), $error->getMessage());
            $this->eventBus->dispatch(new LogFileEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
