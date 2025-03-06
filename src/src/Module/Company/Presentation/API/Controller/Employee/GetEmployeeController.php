<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetEmployeeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Get(
        path: '/api/employees/{uuid}',
        summary: 'Pobiera pracownika',
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'UUID pracownika',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Pracownik został pobrany',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2025-02-16T22:05:18+00:00'),
                                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2025-02-16T22:05:18+00:00'),
                                new OA\Property(property: 'deletedAt', type: 'string', format: 'date-time', example: null, nullable: true),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd niepoprawnego UUID',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Wystapił błąd przy pobieraniu wybranego pracownika. Pracownik o podanym UUID nie istnieje : 64a42efa-98ce-426a-9123-f44265ae96c'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'employees')]
    #[Route('/api/employees/{uuid}', name: 'api.employees.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => json_decode($this->serializer->serialize(
                    $this->employeeReaderRepository->getEmployeeByUUID($uuid),
                    'json', ['groups' => [
                        'employee_info',
                        'company_info',
                        'department_info',
                        'role_info',
                        'contract_type_info',
                        'position_info',
                        'industry_info',
                        'address_info',
                        'contact_info',
                    ]],
                )),
            ], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('employee.view.error', [], 'employees'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
