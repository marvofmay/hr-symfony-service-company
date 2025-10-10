<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Department\CreateDTO;
use App\Module\Company\Presentation\API\Action\Department\CreateDepartmentAction;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateDepartmentController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/departments', name: 'api.departments.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateDepartmentAction $createDepartmentAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::CREATE, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $createDepartmentAction->execute($createDTO);

            return new JsonResponse(['message' => $this->messageService->get('department.add.success', [], 'departments')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('department.add.error', [], 'departments'), $error->getMessage());
            $this->eventBus->dispatch(new LogFileEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
