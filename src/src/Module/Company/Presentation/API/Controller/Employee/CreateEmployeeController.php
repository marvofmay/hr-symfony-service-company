<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Employee\CreateDTO;
use App\Module\Company\Presentation\API\Action\Employee\CreateEmployeeAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class CreateEmployeeController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly MessageService $messageService,)
    {
    }

    #[Route('/api/employees', name: 'api.employees.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateEmployeeAction $createEmployeeAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::CREATE, AccessEnum::EMPLOYEE)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $createEmployeeAction->execute($createDTO);

            return new JsonResponse(['message' => $this->messageService->get('employee.add.success', [], 'employees')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('employee.add.error', [], 'employees'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
