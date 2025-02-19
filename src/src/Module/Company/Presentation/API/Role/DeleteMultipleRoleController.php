<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Module\Company\Domain\DTO\Role\DeleteMultipleDTO;
use App\Module\Company\Domain\Action\Role\DeleteMultipleRolesAction;
use Exception;

class DeleteMultipleRoleController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator) {}

    #[Route('/api/roles/multiple', name: 'api.roles.deletemultiple', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO, DeleteMultipleRolesAction $deleteMultipleRolesAction): JsonResponse
    {
        try {
            $deleteMultipleRolesAction->execute($deleteMultipleDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.delete.multiple.success', [], 'roles')],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.delete.multiple.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}