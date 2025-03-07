<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Module\Company\Application\Query\Company\GetCompaniesQuery;
use App\Module\Company\Application\QueryHandler\Company\GetCompaniesQueryHandler;
use App\Module\Company\Domain\DTO\Company\CompaniesQueryDTO;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListCompaniesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Get(
        path: '/api/companies',
        summary: 'Pobiera listę firm',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Lista firm',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: [
                            new OA\Property(property: 'totalCompanies', type: 'integer', example: 13),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'limit', type: 'integer', example: 10),
                            new OA\Property(property: 'companies', type: 'array', items: new OA\Items(properties: [
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
                description: 'Błąd przy pobieraniu listy firm',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Wystapił błąd przy pobieraniu listy firm'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'companies')]
    #[Route('/api/companies', name: 'api.companies.list', methods: ['GET'])]
    public function list(#[MapQueryString] CompaniesQueryDTO $queryDTO, GetCompaniesQueryHandler $companiesQueryHandler): Response
    {
        try {
            return new JsonResponse([
                'data' => json_decode($this->serializer->serialize(
                    $companiesQueryHandler->handle(new GetCompaniesQuery($queryDTO)),
                    'json', ['groups' => ['company_info']],
                )),
            ],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('company.list.error', [], 'companies'), $error->getMessage())
            );

            return new JsonResponse(
                [
                    'data' => [],
                    'message' => $this->translator->trans('company.list.error', [], 'companies'),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
