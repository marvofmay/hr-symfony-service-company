<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Application\Query\Role\GetRolesQuery;
use App\Module\Company\Application\QueryHandler\Role\GetRolesQueryHandler;
use App\Module\Company\Presentation\Request\Role\ListingRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/api/roles', name: 'api.roles.list', methods: ['GET'])]
    public function list(Request $request, GetRolesQueryHandler $usersQueryHandler): Response
    {
        try {
            return new JsonResponse([
                'data' =>
                    json_decode($this->serializer->serialize(
                        $usersQueryHandler->handle(new GetRolesQuery(new ListingRequest($request))),
                        'json', ['groups' => ['role_info']],
                    ))
                ],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('role.list.error', [], 'roles'), $error->getMessage())
            );

            return new JsonResponse(
                [
                    'data' => [],
                    'message' => $this->translator->trans('role.list.error', [], 'roles'),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}