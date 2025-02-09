<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/roles', name: 'api.roles.')]
class GetRoleController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly SerializerInterface $serializer,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/{uuid}', name: 'get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => json_decode($this->serializer->serialize(
                    $this->roleReaderRepository->getRoleByUUID($uuid),
                    'json', ['groups' => ['role_info']],
                ))
            ], Response::HTTP_OK);
        } catch (Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.view.error', [], 'roles'),  $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}