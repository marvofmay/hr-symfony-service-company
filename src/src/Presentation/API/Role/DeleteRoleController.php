<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Domain\Action\Role\DeleteRoleAction;
use App\Domain\Interface\Role\RoleReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/roles', name: 'api.roles.')]
class DeleteRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $uuid, DeleteRoleAction $deleteRoleAction): JsonResponse
    {
        try {
            $deleteRoleAction->setRoleToDelete($this->roleReaderRepository->getRoleByUUID($uuid))
                ->execute();
            return new JsonResponse(
                ['message' => $this->translator->trans('role.deleted.success')],
                Response::HTTP_OK
            );

        } catch (Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('role.delete.error'), $error->getMessage())
            );

            return new JsonResponse(['message' => $this->translator->trans('role.delete.error')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}