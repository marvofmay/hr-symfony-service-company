<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Module\Company\Domain\DTO\Role\CreateAccessPermissionDTO;
use App\Module\Company\Presentation\API\Action\Role\CreateRoleAccessPermissionAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateRoleAccessPermissionController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[OA\Post(
        path: '/api/roles/{uuid}/accesses/permissions',
        summary: 'Tworzy uprawnienia dla roli',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: CreateAccessPermissionDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Uprawnienia dla roli zostały utworzone',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Uprawnienia dla roli zostały pomyślnie dodane'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Rola nie istnieje'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'roles')]
    #[Route('/api/roles/{uuid}/accesses/permissions', name: 'api.roles.accesses.permissions.create', methods: ['POST'])]
    public function create(string $uuid, #[MapRequestPayload] CreateAccessPermissionDTO $createAccessPermissionDTO, CreateRoleAccessPermissionAction $createRoleAccessPermissionAction): JsonResponse
    {
        try {
            if ($uuid !== $createAccessPermissionDTO->getRoleUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('role.uuid.differentUUIDInBodyRawAndUrl', [], 'roles')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $createRoleAccessPermissionAction->execute($createAccessPermissionDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.add.permission.success', [], 'roles')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.add.permission.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
