<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Module\Note\Domain\Enum\NotePriorityEnum;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/notes', name: 'api.notes.')]
class GetNoteController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly NoteReaderInterface $noticeReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Get(
        path: '/api/notes/{uuid}',
        summary: 'Pobiera notatkę',
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
                description: 'Notatka została pobrana',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '64a42efa-98ce-426a-9123-f44265ae96cc'),
                                new OA\Property(property: 'title', type: 'string', example: 'Zadzwoń!!!'),
                                new OA\Property(property: 'content', type: 'string', example: 'O 12:00 zadzwoń do przełożonego'),
                                new OA\Property(
                                    property: 'priority',
                                    description: 'Priorytet tworzonej notatki (możliwe wartości: low, medium, high)',
                                    type: 'string',
                                    enum: [NotePriorityEnum::LOW, NotePriorityEnum::MEDIUM, NotePriorityEnum::HIGH],
                                    example: NotePriorityEnum::HIGH,
                                    nullable: false
                                ),
                                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-02-16T22:05:18+00:00'),
                                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: null, nullable: true),
                                new OA\Property(property: 'deletedAt', type: 'string', format: 'date-time', example: null, nullable: true),
                            ],
                            type: 'object'
                        ),
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
                            example: 'Wystapił błąd przy pobieraniu wybranej notatki: Notatka o podanym UUID nie istnieje : 64a42efa-98ce-426a-9123-f44265ae96c'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'notes')]    
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
