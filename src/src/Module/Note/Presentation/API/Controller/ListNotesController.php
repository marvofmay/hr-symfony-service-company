<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Module\Note\Application\Query\GetNotesQuery;
use App\Module\Note\Application\QueryHandler\GetNotesQueryHandler;
use App\Module\Note\Domain\DTO\NotesQueryDTO;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListNotesController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator,) 
    {}

    #[OA\Get(
        path: '/api/notes',
        summary: 'Pobiera listę notatek',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Lista notatek',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: [
                            new OA\Property(property: 'totalNotes', type: 'integer', example: 13),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'limit', type: 'integer', example: 10),
                            new OA\Property(property: 'notes', type: 'array', items: new OA\Items(properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9c1963a3-cb27-4e6a-b474-3509ed4b3457'),
                                new OA\Property(property: 'title', type: 'string', example: 'To jest tytuł notatki'),
                                new OA\Property(property: 'content', type: 'string', example: 'To jest treść notatki ...'),
                                new OA\Property(
                                    property: 'priority',
                                    type: 'string',
                                    enum: ['low', 'medium', 'high'],
                                    example: 'low'
                                ),
                                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-02-09T18:56:07+00:00'),
                                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2025-02-10T10:42:55+00:00'),
                                new OA\Property(property: 'deletedAt', type: 'string', format: 'date-time', example: null, nullable: true),
                            ])),
                        ], type: 'object'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd przy pobieraniu listy notatek',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Wystapił błąd przy pobieraniu listy notatek'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'notes')]
    #[Route('/api/notes', name: 'api.notes.list', methods: ['GET'])]
    public function list(#[MapQueryString] NotesQueryDTO $queryDTO, GetNotesQueryHandler $notesQueryHandler): Response
    {
        try {
            return new JsonResponse(['data' => $notesQueryHandler->handle(new GetNotesQuery($queryDTO)),], Response::HTTP_OK);
        } catch (\Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('note.list.error', [], 'notes'), $error->getMessage())
            );

            return new JsonResponse(
                [
                    'data' => [],
                    'message' => $this->translator->trans('note.list.error', [], 'notes'),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
