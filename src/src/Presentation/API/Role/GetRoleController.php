<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Domain\Interface\Role\RoleReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/roles', name: 'api.roles.')]
class GetRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/{uuid}', name: 'get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            return $this->json([
                'data' => json_decode($this->serializer->serialize(
                    $this->roleReaderRepository->getRoleByUUID($uuid),
                    'json', ['groups' => ['role_info']],
                ))
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('get role by uuid: ' . $e->getMessage());

            return new JsonResponse(['errors' => 'Upss... problem with get role data'],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}