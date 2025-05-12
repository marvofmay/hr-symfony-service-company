<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Module\Company\Domain\DTO\Role\CreateDTO;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Presentation\API\Action\Role\CreateRoleAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\ModuleEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateRoleController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(PermissionEnum::CREATE->value, AccessEnum::ROLE);
    }

    #[Route('/api/roles', name: 'api.roles.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateRoleAction $createRoleAction): JsonResponse
    {
        try {
            $createRoleAction->execute($createDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.add.success', [], 'roles')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.add.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
