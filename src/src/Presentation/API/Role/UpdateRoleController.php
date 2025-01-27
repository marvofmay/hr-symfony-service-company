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
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/roles', name: 'api.roles.')]
class UpdateRoleController extends AbstractController
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateRoleAction $updateRoleAction): Response
    {
        try {
            if ($uuid !== $updateDTO->getUUID()) {
                return $this->json(
                    ['message' => $this->translator->trans('role.uuid.differentUUIDInBodyRawAndUrl')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $updateRoleAction->setRoleToUpdate($this->roleReaderRepository->getRoleByUUID($uuid))
                ->execute($updateDTO);

            return $this->json(
                ['message' => $this->translator->trans('role.update.success')],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('role.update.error'), $error->getMessage())
            );

            return $this->json(
                ['message' => $this->translator->trans('role.update.error')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}