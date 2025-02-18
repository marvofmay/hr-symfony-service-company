<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Domain\Action\Role\UpdateRoleAction;
use App\Module\Company\Domain\DTO\Role\UpdateDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;

class UpdateRoleController extends AbstractController
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/api/roles/{uuid}', name: 'api.roles.update', methods: ['PUT'])]
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
                response: 200,
                description: "Rola została zaktualizowana",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Rola została pomyślnie zaktualizowana"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Błąd walidacji",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Oczekiwano unikalnej nazwy roli"),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'roles')]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateRoleAction $updateRoleAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('role.uuid.differentUUIDInBodyRawAndUrl', [], 'roles')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $updateRoleAction->setRoleToUpdate($this->roleReaderRepository->getRoleByUUID($uuid));
            $updateRoleAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('role.update.success', [], 'roles')], Response::HTTP_OK);
        } catch (Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.update.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}