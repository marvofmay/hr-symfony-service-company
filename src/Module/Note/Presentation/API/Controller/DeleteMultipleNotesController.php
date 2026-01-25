<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Note\Application\Command\DeleteMultipleNotesCommand;
use App\Module\Note\Application\DTO\DeleteMultipleDTO;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class DeleteMultipleNotesController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'command.bus')] private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/users/notes/multiple', name: 'api.notes.delete_multiple', methods: ['DELETE'])]
    public function __invoke(#[MapRequestPayload] DeleteMultipleDTO $dto): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionEnum::DELETE, AccessEnum::NOTES, $this->messageService->get('accessDenied'));

        try {
            $this->commandBus->dispatch(new DeleteMultipleNotesCommand($dto->notesUUIDs));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new JsonResponse(['message' => $this->messageService->get('note.delete.multiple.success', [], 'notes')], Response::HTTP_OK);
    }
}
