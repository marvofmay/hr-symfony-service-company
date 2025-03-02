<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Module\Company\Domain\DTO\ContractType\UpdateDTO;
use App\Module\Company\Presentation\API\Action\ContractType\UpdateContractTypeAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateContractTypeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Put(
        path: '/api/contract_types/{uuid}',
        summary: 'Aktualizuje formę zatrudnienia',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: UpdateDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Forma zatrudnienia została zaktualizowana',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Forma zatrudnienia została pomyślnie zaktualizowana'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Oczekiwano unikalnej nazwy formy zatrudnienia'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'contract_types')]
    #[Route('/api/contract_types/{uuid}', name: 'api.contract_types.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateContractTypeAction $updateContractTypeAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $updateContractTypeAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('contractType.update.success', [], 'contract_types')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('contractType.update.error', [], 'contract_types'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
