<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Module\Company\Domain\DTO\Company\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Company\UpdateCompanyAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateCompanyController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[OA\Put(
        path: '/api/companies/{uuid}',
        summary: 'Aktualizuje firmę',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: UpdateDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Firma została zaktualizowana',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Firma zostało pomyślnie zaktualizowana'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Firma o podanej nazwie już istnieje'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'companies')]
    #[Route('/api/companies/{uuid}', name: 'api.company.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateCompanyAction $updateCompanyAction): JsonResponse
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators')],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $updateCompanyAction->execute($updateDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('company.update.success', [], 'companies')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('company.update.error', [], 'companies'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
