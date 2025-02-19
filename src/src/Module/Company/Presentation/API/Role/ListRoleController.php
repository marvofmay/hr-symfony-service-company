<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Application\Query\Role\GetRolesQuery;
use App\Module\Company\Application\QueryHandler\Role\GetRolesQueryHandler;
use App\Module\Company\Presentation\Request\Role\ListingRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;

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
        parameters: [
            new OA\Parameter(
                name: "phrase",
                description: "Fraza do wyszukania",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "name",
                description: "Nazwa roli",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "description",
                description: "Opis roli",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "createdAt",
                description: "Data utworzenia",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date-time")
            ),
            new OA\Parameter(
                name: "updatedAt",
                description: "Data aktualizacji",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date-time")
            ),
            new OA\Parameter(
                name: "deletedAt",
                description: "Data usunięcia",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date-time")
            ),
            new OA\Parameter(
                name: "page",
                description: "Numer strony wyników",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "pageSize",
                description: "Liczba wyników na stronę",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            ),
            new OA\Parameter(
                name: "sortBy",
                description: "Pole do sortowania (name, description, createdAt, updatedAt, deletedAt)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string",default: "createdAt", enum: ["name", "description", "createdAt", "updatedAt", "deletedAt"])
            ),
            new OA\Parameter(
                name: "sortDirection",
                description: "Kierunek sortowania (asc lub desc)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", default: "desc", enum: ["asc", "desc"])
            ),
            new OA\Parameter(
                name: "deleted",
                description: "Flaga pobierania usuniętych ról (1 - pobiera usunięte, 0 - pobiera aktywne)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", enum: [0, 1])
            ),
        ],
        responses: [
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
    public function list(Request $request, GetRolesQueryHandler $usersQueryHandler): Response
    {
        $x = 1;

        try {
            return new JsonResponse([
                'data' =>
                    json_decode($this->serializer->serialize(
                        $usersQueryHandler->handle(new GetRolesQuery(new ListingRequest($request))),
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