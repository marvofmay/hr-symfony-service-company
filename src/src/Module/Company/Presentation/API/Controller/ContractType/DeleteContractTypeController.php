<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Module\Company\Presentation\API\Action\ContractType\DeleteContractTypeAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteContractTypeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Delete(
        path: '/api/contract_types/{uuid}',
        summary: 'Usuwa formę zatrudnienia - soft delete',
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'UUID formy zatrudnienia',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Forma zatrudnienia została usunięta',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Forma zatrudnienia została pomyślnie usunięta'),
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
                            example: 'Wystąpił błąd - forma zatrudnienia nie została usunięta: Forma zatrudnienia o podanym UUID nie istnieje : e8933421-84a2-4846-b3e4-b3a4ffbda1a'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'contract_types')]
    #[Route('/api/contract_types/{uuid}', name: 'api.contract_types.delete', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['DELETE'])]
    public function delete(string $uuid, DeleteContractTypeAction $deleteContractTypeAction): JsonResponse
    {
        try {
            $deleteContractTypeAction->execute($uuid);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.delete.success', [], 'contract_types')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.delete.error', [], 'contract_types'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
