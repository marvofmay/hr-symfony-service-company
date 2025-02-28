<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Presentation\API\Action\Position\DeletePositionAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeletePositionController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Delete(
        path: '/api/positions/{uuid}',
        summary: 'Usuwa stanowisko - soft delete',
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'UUID stanowiska',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Stanowisko zostało usunięte',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Stanowisko zostało pomyślnie usunięte'),
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
                            property: 'error',
                            type: 'string',
                            example: 'Wystąpił błąd - stanowisko nie została usunięta. Stanowisko o podanym UUID nie istnieje : e8933421-84a2-4846-b3e4-b3a4ffbda1a'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'positions')]
    #[Route('/api/positions/{uuid}', name: 'api.positions.delete', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['DELETE'])]
    public function delete(string $uuid, DeletePositionAction $deletePositionAction): JsonResponse
    {
        try {
            $deletePositionAction->execute($uuid);

            return new JsonResponse(
                ['message' => $this->translator->trans('position.delete.success', [], 'positions')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('position.delete.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
