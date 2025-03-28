<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Domain\DTO\Position\CreateDTO;
use App\Module\Company\Presentation\API\Action\Position\CreatePositionAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreatePositionController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[OA\Post(
        path: '/api/positions',
        summary: 'Tworzy nowe stanowisko',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: CreateDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Stanowisko zostało utworzone',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Stanowisko zostało pomyślnie dodane'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Stanowisko istnieje'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'positions')]
    #[Route('/api/positions', name: 'api.positions.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreatePositionAction $createPositionAction): JsonResponse
    {
        try {
            $createPositionAction->execute($createDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('position.add.success', [], 'positions')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('position.add.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
