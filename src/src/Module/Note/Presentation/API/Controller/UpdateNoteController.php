<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Note\Application\Command\UpdateNoteCommand;
use App\Module\Note\Domain\DTO\UpdateDTO;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class UpdateNoteController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/employees/notes/{uuid}', name: 'api.employees.notes.update', methods: ['PUT'])]
    public function __invoke(string $uuid, #[MapRequestPayload] UpdateDTO $dto): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::UPDATE, AccessEnum::NOTE, $this->messageService->get('accessDenied'));

        try {
            $this->commandBus->dispatch(
                new UpdateNoteCommand(
                    noteUUID: $uuid,
                    title: $dto->title,
                    content: $dto->content,
                    priority: $dto->priority
                )
            );
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new JsonResponse(['message' => $this->messageService->get('note.update.success', [], 'notes')], Response::HTTP_OK);
    }
}
