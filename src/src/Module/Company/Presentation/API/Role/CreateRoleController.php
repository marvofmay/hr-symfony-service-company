<?php

declare(strict_types = 1);

namespace App\Module\Company\Presentation\API\Role;

use App\Module\Company\Domain\Action\Role\CreateRoleAction;
use App\Module\Company\Domain\DTO\Role\CreateDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Translation\TranslatorInterface;
use Exception;

#[Route('/api/roles', name: 'api.roles.')]
class CreateRoleController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateRoleAction $createRoleAction): JsonResponse
    {
        try {
            $createRoleAction->execute($createDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('role.add.success', [], 'roles')],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.add.error', [], 'roles'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}