<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Domain\Action\Role\UpdateRoleAction;
use App\Domain\DTO\Role\UpdateDTO;
use App\Domain\Interface\Role\RoleReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/api/roles', name: 'api.roles.')]
class UpdateRoleController extends AbstractController
{
    public function __construct(private readonly RoleReaderInterface $roleReaderRepository, private readonly LoggerInterface $logger) {}

    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateRoleAction $updateRoleAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(['errors' => 'Different UUID in body raw and url'], Response::HTTP_BAD_REQUEST);
            }

            $updateRoleAction->setRoleToUpdate($this->roleReaderRepository->getRoleByUUID($uuid))
                ->execute($updateDTO);

            return $this->json(['message' => 'Role has been updated.'], Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logger->error('trying role update: ' .  $e->getMessage());

            return $this->json(['errors' => 'Upss... Problem with update role.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}