<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API;

use App\Module\Note\Domain\Action\DeleteNoteAction;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/notes', name: 'api.notes.')]
class DeleteNoteController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly NoteReaderInterface $noteReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $uuid, DeleteNoteAction $deleteNoteAction): JsonResponse
    {
        try {
            $deleteNoteAction->setNoteToDelete($this->noteReaderRepository->getNoteByUUID($uuid))
                ->execute();

            return new JsonResponse(
                ['message' => $this->translator->trans('note.delete.success', [], 'notes')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('note.delete.error', [], 'notes'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
