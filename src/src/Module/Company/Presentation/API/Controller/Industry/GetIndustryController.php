<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetIndustryController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly IndustryReaderInterface $industryReaderRepository,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Get(
        path: '/api/industries/{uuid}',
        summary: 'Pobiera branżę',
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'UUID branży',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Branża została pobrana',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '64a42efa-98ce-426a-9123-f44265ae96cc'),
                                new OA\Property(property: 'name', type: 'string', example: 'Rolnictwo'),
                                new OA\Property(property: 'description', type: 'string', example: 'Branża rolnictwo ...'),
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
                            example: 'Wystapił błąd przy pobieraniu wybranej branży: Branża o podanym UUID nie istnieje : 64a42efa-98ce-426a-9123-f44265ae96c'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'industries')]
    #[Route('/api/industries/{uuid}', name: 'api.industries.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => json_decode($this->serializer->serialize(
                    $this->industryReaderRepository->getIndustryByUUID($uuid),
                    'json', ['groups' => ['industry_info']],
                )),
            ], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('industry.view.error', [], 'industries'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
