<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Module\Company\Application\Query\Department\GetDepartmentsQuery;
use App\Module\Company\Application\QueryHandler\Department\GetDepartmentsQueryHandler;
use App\Module\Company\Domain\DTO\Department\DepartmentsQueryDTO;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListDepartmentsController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Get(
        path: '/api/departments',
        summary: 'Pobiera listę departmentów',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Lista departmentów',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: [
                            new OA\Property(property: 'totalDepartments', type: 'integer', example: 13),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'limit', type: 'integer', example: 10),
                            new OA\Property(property: 'departments', type: 'array', items: new OA\Items(properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9c1963a3-cb27-4e6a-b474-3509ed4b3457'),
                                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-02-09T18:56:07+00:00'),
                                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2025-02-10T10:42:55+00:00'),
                                new OA\Property(property: 'deletedAt', type: 'string', format: 'date-time', example: null, nullable: true),
                            ])),
                        ], type: 'object'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd przy pobieraniu listy departmentów',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Wystapił błąd przy pobieraniu listy departmentów'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'departments')]
    #[Route('/api/departments', name: 'api.departments.list', methods: ['GET'])]
    public function list(#[MapQueryString] DepartmentsQueryDTO $queryDTO, GetDepartmentsQueryHandler $departmentsQueryHandler): Response
    {
        try {
            //ToDo:: refactor - use query.bus
            return new JsonResponse([
                'data' => json_decode($this->serializer->serialize(
                    $departmentsQueryHandler->handle(new GetDepartmentsQuery($queryDTO)),
                    'json', ['groups' => ['department_info']],
                )),
            ],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('department.list.error', [], 'departments'), $error->getMessage())
            );

            return new JsonResponse(
                [
                    'data' => [],
                    'message' => $this->translator->trans('department.list.error', [], 'departments'),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
