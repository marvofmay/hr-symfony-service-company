<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Command\Company\DeleteMultipleCompaniesCommand;
use App\Module\Company\Domain\DTO\Company\DeleteMultipleDTO;
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

class DeleteMultipleCompaniesController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
        private readonly MessageService $messageService,
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    #[Route('/api/companies/multiple', name: 'api.companies.delete_multiple', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::DELETE,
                AccessEnum::COMPANY,
                $this->messageService->get('accessDenied')
            );

            $this->dispatchCommand($deleteMultipleDTO);

            return $this->successResponse();
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchCommand(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        try {
            $this->commandBus->dispatch(
                new DeleteMultipleCompaniesCommand($deleteMultipleDTO->selectedUUIDs)
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(): JsonResponse
    {
        return new JsonResponse(
            ['message' => $this->messageService->get('company.multipleDelete.success', [], 'companies')],
            Response::HTTP_NO_CONTENT
        );
    }

    private function errorResponse(\Throwable $exception): JsonResponse
    {
        $message = sprintf(
            '%s. %s',
            $this->messageService->get('company.multipleDelete.error', [], 'companies'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::EVENT_STORE));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
