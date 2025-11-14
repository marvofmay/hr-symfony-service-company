<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\DTO\Industry\UpdateDTO;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateIndustryController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'event.bus')] private readonly MessageBusInterface $eventBus,
        #[Autowire(service: 'command.bus')] private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/industries/{uuid}', name: 'api.industries.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO): Response
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::UPDATE,
                AccessEnum::INDUSTRY,
                $this->messageService->get('accessDenied')
            );
            $this->dispatchCommand($uuid, $updateDTO);

            return $this->successResponse();
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchCommand(string $uuid, UpdateDTO $updateDTO): void
    {
        try {
            $this->commandBus->dispatch(
                new UpdateIndustryCommand(
                    industryUUID: $uuid,
                    name: $updateDTO->name,
                    description: $updateDTO->description,
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(): JsonResponse
    {
        return new JsonResponse(
            ['message' => $this->messageService->get('industry.update.success', [], 'industries')],
            Response::HTTP_OK
        );
    }

    private function errorResponse(\Throwable $exception): JsonResponse
    {
        $message = sprintf(
            '%s %s',
            $this->messageService->get('industry.update.error', [], 'industries'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::EVENT_LOG));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
