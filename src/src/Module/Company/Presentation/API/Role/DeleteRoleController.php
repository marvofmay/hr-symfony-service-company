<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Domain\Action\Role\DeleteRoleAction;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;

class DeleteRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly TranslatorInterface $translator
    ) {}

    #[OA\Delete(
        path: '/api/roles/{uuid}',
        summary: 'Usuwa rolę - soft delete',
        parameters: [
            new OA\Parameter(
                name: "uuid",
                description: "UUID roli",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Rola została usunięta",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Rola została pomyślnie usunięta"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Błąd niepoprawnego UUID",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Wystąpił błąd - rola nie została usunięta: Rola o podanym UUID nie istnieje : e8933421-84a2-4846-b3e4-b3a4ffbda1a"
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'roles')]
    #[Route('/api/roles/{uuid}', name: 'api.roles.delete', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['DELETE'])]
    public function delete(string $uuid, DeleteRoleAction $deleteRoleAction): JsonResponse
    {
        try {
            $deleteRoleAction->setRoleToDelete($this->roleReaderRepository->getRoleByUUID($uuid))
                ->execute();
            return new JsonResponse(
                ['message' => $this->translator->trans('role.delete.success', [], 'roles')],
                Response::HTTP_OK
            );

        } catch (Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.delete.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}