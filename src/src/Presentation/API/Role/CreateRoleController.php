<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Domain\Action\Role\CreateRoleAction;
use App\Domain\DTO\Role\CreateDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/api/roles', name: 'api.roles.')]
class CreateRoleController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger) { }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateRoleAction $createRoleAction): JsonResponse
    {
        try {
            $createRoleAction->execute($createDTO);

            return new JsonResponse(['message' => 'Role has been created.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('trying create new role: ' .  $e->getMessage());

            return new JsonResponse(['errors' => 'Upss... problem with create role'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}