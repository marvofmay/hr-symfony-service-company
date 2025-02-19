<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Application\Query\Role\GetRolesQuery;
use App\Module\Company\Application\QueryHandler\Role\GetRolesQueryHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Module\Company\Domain\DTO\Role\RolesQueryDTO;

class ListRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator
    ) {}

    #[OA\Get(
        path: '/api/roles',
        summary: 'Pobiera listę ról',
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista ról",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "totalRoles", type: "integer", example: 13),
                            new OA\Property(property: "page", type: "integer", example: 1),
                            new OA\Property(property: "limit", type: "integer", example: 10),
                            new OA\Property(property: "roles", type: "array", items: new OA\Items(properties: [
                                new OA\Property(property: "uuid", type: "string", format: "uuid", example: "9c1963a3-cb27-4e6a-b474-3509ed4b3457"),
                                new OA\Property(property: "name", type: "string", example: "rola 8"),
                                new OA\Property(property: "description", type: "string", example: "Lorem ipsum dolor sit amet..."),
                                new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "2025-02-09T18:56:07+00:00"),
                                new OA\Property(property: "updatedAt", type: "string", format: "date-time", example: "2025-02-10T10:42:55+00:00"),
                                new OA\Property(property: "deletedAt", type: "string", format: "date-time", example: null, nullable: true),
                            ])),
                        ], type: "object"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Błąd przy pobieraniu listy ról",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Wystapił błąd przy pobieraniu listy ról"
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'roles')]
    #[Route('/api/roles', name: 'api.roles.list', methods: ['GET'])]
    public function list(#[MapQueryString] RolesQueryDTO $queryDTO, GetRolesQueryHandler $usersQueryHandler): Response
    {
        try {
            return new JsonResponse([
                'data' =>
                    json_decode($this->serializer->serialize(
                        $usersQueryHandler->handle(new GetRolesQuery($queryDTO)),
                        'json', ['groups' => ['role_info']],
                    ))
                ],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('role.list.error', [], 'roles'), $error->getMessage())
            );

            return new JsonResponse(
                [
                    'data' => [],
                    'message' => $this->translator->trans('role.list.error', [], 'roles'),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}