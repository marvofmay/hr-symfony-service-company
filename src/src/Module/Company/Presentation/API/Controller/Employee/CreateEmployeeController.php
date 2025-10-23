<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
use App\Module\Company\Domain\DTO\Employee\CreateDTO;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateEmployeeController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
        private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/employees', name: 'api.employees.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::CREATE,
                AccessEnum::EMPLOYEE,
                $this->messageService->get('accessDenied')
            );

            $this->dispatchCommand($createDTO);

            return $this->successResponse();
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchCommand(CreateDTO $createDTO): void
    {
        try {
            $this->commandBus->dispatch(
                new CreateEmployeeCommand(
                    $createDTO->departmentUUID,
                    $createDTO->positionUUID,
                    $createDTO->contractTypeUUID,
                    $createDTO->roleUUID,
                    $createDTO->parentEmployeeUUID,
                    $createDTO->externalUUID,
                    $createDTO->internalCode,
                    $createDTO->email,
                    $createDTO->firstName,
                    $createDTO->lastName,
                    $createDTO->pesel,
                    $createDTO->employmentFrom,
                    $createDTO->employmentTo,
                    $createDTO->active,
                    $createDTO->phones,
                    $createDTO->address
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(): JsonResponse
    {
        return new JsonResponse(
            ['message' => $this->messageService->get('employee.add.success', [], 'employees')],
            Response::HTTP_CREATED
        );
    }

    private function errorResponse(\Throwable $exception): JsonResponse
    {
        $message = sprintf(
            '%s. %s',
            $this->messageService->get('employee.add.error', [], 'employees'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::EVENT_STORE));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
