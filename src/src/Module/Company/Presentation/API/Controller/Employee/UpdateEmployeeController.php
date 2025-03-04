<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Module\Company\Domain\DTO\Employee\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Employee\UpdateEmployeeAction;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateEmployeeController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[OA\Put(
        path: '/api/employees/{uuid}',
        summary: 'Aktualizuje pracownika',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: UpdateDTO::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Pracownik został zaktualizowany',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Pracownik został pomyślnie zaktualizowany'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Pracownik o podanym emailu już istnieje'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'employees')]
    #[Route('/api/employees/{uuid}', name: 'api.employee.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateEmployeeAction $updateEmployeeAction): JsonResponse
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators')],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $updateEmployeeAction->execute($updateDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('employee.update.success', [], 'employees')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('employee.update.error', [], 'employees'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
