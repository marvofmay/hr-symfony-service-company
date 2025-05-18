<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Module\Company\Domain\DTO\Role\CreateAccessPermissionDTO;
use App\Module\Company\Presentation\API\Action\Role\CreateRoleAccessPermissionAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateRoleAccessPermissionController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/roles/{uuid}/accesses/permissions', name: 'api.roles.accesses.permissions.create', methods: ['POST'])]
    public function create(string $uuid, #[MapRequestPayload] CreateAccessPermissionDTO $createAccessPermissionDTO, CreateRoleAccessPermissionAction $createRoleAccessPermissionAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::ASSIGN_PERMISSION_TO_ACCESS_ROLE, AccessEnum::PERMISSION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            if ($uuid !== $createAccessPermissionDTO->getRoleUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('role.uuid.differentUUIDInBodyRawAndUrl', [], 'roles')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $createRoleAccessPermissionAction->execute($createAccessPermissionDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.add.permission.success', [], 'roles')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.add.permission.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
