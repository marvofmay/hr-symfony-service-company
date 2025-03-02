<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Module\Note\Domain\DTO\UpdateDTO;
use App\Module\Note\Presentation\API\Action\UpdateNoteAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/notes', name: 'api.notes.')]
class UpdateNoteController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Put(
        path: '/api/notes/{uuid}',
        summary: 'Aktualizuje notatkę',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: UpdateDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Notatka została zaktualizowana',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Notatka została pomyślnie zaktualizowana'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "errors",
                            properties: [
                                new OA\Property(
                                    property: "uuid",
                                    type: "string",
                                    example: "Ta wartość nie powinna być pusta."
                                )
                            ],
                            type: "object"
                        )
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'notes')]
    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateNoteAction $updateNoteAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('note.uuid.differentUUIDInBodyRawAndUrl', [], 'notes')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $updateNoteAction->execute($updateDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('note.update.success', [], 'notes')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('note.update.error', [], 'notes'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(
                ['message' => $message],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
