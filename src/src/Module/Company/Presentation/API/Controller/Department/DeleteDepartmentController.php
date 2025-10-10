<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Presentation\API\Action\Department\DeleteDepartmentAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteDepartmentController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/departments/{uuid}', name: 'api.departments.delete', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['DELETE'])]
    public function delete(string $uuid, DeleteDepartmentAction $deleteDepartmentAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::DELETE, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->messageService->get('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $deleteDepartmentAction->execute($uuid);

            return new JsonResponse(['message' => $this->messageService->get('department.delete.success', [], 'departments')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('department.delete.error', [], 'departments'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
