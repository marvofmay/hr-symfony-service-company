<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Module\Company\Domain\DTO\Role\RolesQueryDTO;
use App\Module\Company\Presentation\API\Action\Role\AskRolesAction;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListRolesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/roles', name: 'api.roles.list', methods: ['GET'])]
    public function list(#[MapQueryString] RolesQueryDTO $queryDTO, AskRolesAction $askRolesAction): Response
    {
        try {
            return new JsonResponse(['data' => $askRolesAction->ask($queryDTO)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $this->logger->error(sprintf('%s: %s', $this->translator->trans('role.list.error', [], 'roles'), $error->getMessage()));

            return new JsonResponse(['data' => [], 'message' => $this->translator->trans('role.list.error', [], 'roles'),], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
