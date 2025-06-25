<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Presentation\API\Action\Industry\DeleteIndustryAction;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteIndustryController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/industries/{uuid}', name: 'api.industries.delete', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['DELETE'])]
    public function delete(string $uuid, DeleteIndustryAction $deleteIndustryAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::DELETE, AccessEnum::INDUSTRY)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            $deleteIndustryAction->execute($uuid);

            return new JsonResponse(['message' => $this->messageService->get('industry.delete.success', [], 'industries')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('industry.delete.error', [], 'industries'), $error->getMessage());
            $this->eventBus->dispatch(new LogFileEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
