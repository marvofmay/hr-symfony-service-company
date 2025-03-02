<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use OpenApi\Attributes as OA;
use App\Module\Note\Presentation\API\Action\DeleteNoteAction;
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
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Delete(
        path: '/api/notes/{uuid}',
        summary: 'Usuwa notatkę - soft delete',
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'UUID notatki',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Notatka została usunięta',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Notatka została pomyślnie usunięta'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd niepoprawnego UUID',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Wystąpił błąd - notatka nie została usunięta: Notatka o podanym UUID nie istnieje : e8933421-84a2-4846-b3e4-b3a4ffbda1a'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'notes')]
    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $uuid, DeleteNoteAction $deleteNoteAction): JsonResponse
    {
        try {
            $deleteNoteAction->execute($uuid);

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
