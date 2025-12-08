<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\DTO\Industry\UpdateDTO;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class UpdateIndustryController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'command.bus')] private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/industries/{uuid}', name: 'api.industries.update', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['PUT'])]
    public function __invoke(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::UPDATE, AccessEnum::INDUSTRIES, $this->messageService->get('accessDenied'));

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

        return new JsonResponse(['message' => $this->messageService->get('industry.update.success', [], 'industries')], Response::HTTP_OK);
    }
}
