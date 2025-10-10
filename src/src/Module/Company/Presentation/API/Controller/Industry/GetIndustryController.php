<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Presentation\API\Action\Industry\AskIndustryAction;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class GetIndustryController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $eventBus, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/industries/{uuid}', name: 'api.industries.get', methods: ['GET'])]
    public function get(string $uuid, AskIndustryAction $askIndustryAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::VIEW, AccessEnum::INDUSTRY)) {
                throw new \Exception($this->messageService->get('accessDenied'), Response::HTTP_FORBIDDEN);
            }

            return new JsonResponse(['data' => $askIndustryAction->ask($uuid)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->messageService->get('industry.view.error', [], 'industries'), $error->getMessage());
            $this->eventBus->dispatch(new LogFileEvent($message));

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
