<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Employee\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Employee\UpdateEmployeeAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateEmployeeController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly MessageService $messageService,)
    {
    }

    #[Route('/api/employees/{uuid}', name: 'api.employee.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateEmployeeAction $updateEmployeeAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::UPDATE, AccessEnum::EMPLOYEE)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $updateEmployeeAction->execute($uuid, $updateDTO);

            return new JsonResponse(['message' => $this->messageService->get('employee.update.success', [], 'employees')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('employee.update.error', [], 'employees'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
