<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Application\Query\Role\GetRolesQuery;
use App\Application\QueryHandler\Role\GetRolesQueryHandler;
use App\Presentation\Request\Role\ListingRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/roles', name: 'api.roles.')]
class ListRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetRolesQueryHandler $usersQueryHandler): Response
    {
        try {
            return $this->json([
                'data' =>
                    json_decode($this->serializer->serialize(
                        $usersQueryHandler->handle(new GetRolesQuery(new ListingRequest($request))),
                        'json', ['groups' => ['role_info']],
                    ))
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            $this->logger->error('show roles: ' . $e->getMessage());

            return new JsonResponse(['data' => [], 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}