<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Module\Company\Presentation\API\Action\Employee\DeleteEmployeeAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteEmployeeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Delete(
        path: '/api/employees/{uuid}',
        summary: 'Usuwa pracownika - soft delete',
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
                description: 'Pracownik została usunięty',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Pracownik zostały pomyślnie usunięty'),
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
                            property: 'message',
                            type: 'string',
                            example: 'Wystąpił błąd - pracownik nie został usunięty. Pracownik o podanym UUID nie istnieje : e8933421-84a2-4846-b3e4-b3a4ffbda1a'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'employees')]
    #[Route('/api/employees/{uuid}', name: 'api.employees.delete', requirements: ['uuid' => '[0-9a-fA-F-]{36}'], methods: ['DELETE'])]
    public function delete(string $uuid, DeleteEmployeeAction $deleteEmployeeAction): JsonResponse
    {
        try {
            $deleteEmployeeAction->execute($uuid);

            return new JsonResponse(
                ['message' => $this->translator->trans('employee.delete.success', [], 'employees')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('employee.delete.error', [], 'employees'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
