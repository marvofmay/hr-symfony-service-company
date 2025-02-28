<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Domain\DTO\Position\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Position\UpdatePositionAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdatePositionController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {}

    #[OA\Put(
        path: '/api/positions/{uuid}',
        summary: 'Aktualizuje rolę',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: UpdateDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Stanowisko zostało zaktualizowane',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Stanowisko zostało pomyślnie zaktualizowane'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Oczekiwano unikalnej nazwy stanowiska'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'positions')]
    #[Route('/api/positions/{uuid}', name: 'api.positions.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdatePositionAction $updatePositionAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $updatePositionAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('position.update.success', [], 'positions')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('position.update.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
