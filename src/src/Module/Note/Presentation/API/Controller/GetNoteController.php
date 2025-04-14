<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/notes', name: 'api.notes.')]
class GetNoteController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly NoteReaderInterface $noticeReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/{uuid}', name: 'get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => $this->noticeReaderRepository->getNoteByUUID($uuid)->toArray(),
            ], Response::HTTP_OK);
        } catch (\Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('note.view.error', [], 'notes'), $error->getMessage())
            );

            return new JsonResponse(
                ['message' => sprintf('%s - %s', $this->translator->trans('note.view.error', [], 'notes'), $error->getMessage())],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
