<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Domain\DTO\Position\PositionsQueryDTO;
use App\Module\Company\Presentation\API\Action\Position\AskPositionsAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListPositionsController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/positions', name: 'api.positions.list', methods: ['GET'])]
    public function list(#[MapQueryString] PositionsQueryDTO $queryDTO, AskPositionsAction $askPositionsAction): Response
    {
        try {
            if (!$this->isGranted(PermissionEnum::LIST, AccessEnum::POSITION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            return new JsonResponse(['data' => $askPositionsAction->ask($queryDTO)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $this->logger->error(sprintf('%s. %s', $this->translator->trans('position.list.error', [], 'positions'), $error->getMessage()));

            return new JsonResponse(['data' => [], 'message' => $this->translator->trans('position.list.error', [], 'positions')], $error->getCode());
        }
    }
}
