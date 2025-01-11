<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Domain\Action\Role\DeleteRoleAction;
use App\Domain\Interface\Role\RoleReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/roles', name: 'api.roles.')]
class DeleteRoleController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly RoleReaderInterface $roleReaderRepository) {}

    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $uuid, DeleteRoleAction $deleteRoleAction): Response
    {
        try {
            $deleteRoleAction->setRoleToDelete($this->roleReaderRepository->getRoleByUUID($uuid))
                ->execute();

            return $this->json(['message' => 'role.deleted.success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('trying role delete: ' .  $e->getMessage());

            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}