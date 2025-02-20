<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Module\Company\Domain\DTO\Role\DeleteMultipleDTO;
use App\Module\Company\Domain\Action\Role\DeleteMultipleRolesAction;
use Exception;
use OpenApi\Attributes as OA;

class DeleteMultipleRoleController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator) {}

    #[OA\Delete(
        path: '/api/roles/multiple',
        operationId: 'deleteMultipleRoles',
        summary: 'Usuń wiele ról - soft delete',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['selectedUUID'],
            properties: [
                new OA\Property(
                    property: 'selectedUUID',
                    type: 'array',
                    items: new OA\Items(type: 'string', format: 'uuid'),
                    example: ['1343b681-39ea-4917-ae2f-7a9296690111', '21e835a2-019c-4aae-a2a7-e20e3ed12871']
                )
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Usunięcie ról zakończone sukcesem',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Role zostały pomyślnie usunięte')
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: 'Błąd walidacji – jedna lub więcej ról nie istnieje',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    example: [
                        'selectedUUID[1]' => 'Rola o podanym UUID nie istnieje 41c77442-78aa-468e-85ca-6d40a78cd558'
                    ],
                    additionalProperties: new OA\AdditionalProperties(
                        type: 'string'
                    )
                )
            ]
        )
    )]
    #[OA\Tag(name: 'roles')]
    #[Route('/api/roles/multiple', name: 'api.roles.delete_multiple', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO, DeleteMultipleRolesAction $deleteMultipleRolesAction): JsonResponse
    {
        try {
            $deleteMultipleRolesAction->execute($deleteMultipleDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.delete.multiple.success', [], 'roles')],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.delete.multiple.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}