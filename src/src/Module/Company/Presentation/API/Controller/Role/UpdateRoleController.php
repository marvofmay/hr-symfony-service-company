<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Module\Company\Domain\DTO\Role\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Role\UpdateRoleAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Put(
        path: '/api/roles/{uuid}',
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
                description: 'Rola została zaktualizowana',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Rola została pomyślnie zaktualizowana'),
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
                                    example: "Rola o podanym UUID nie istnieje 553de6c7-9b8f-46f6-a89e-37a9b3ee907c"
                                ),
                                new OA\Property(
                                    property: "nazwa",
                                    type: "string",
                                    example: "Rola istnieje"
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
    #[OA\Tag(name: 'roles')]
    #[Route('/api/roles/{uuid}', name: 'api.roles.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateRoleAction $updateRoleAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('role.uuid.differentUUIDInBodyRawAndUrl', [], 'roles')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $updateRoleAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('role.update.success', [], 'roles')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.update.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
