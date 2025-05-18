<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Domain\DTO\Position\CreateDTO;
use App\Module\Company\Presentation\API\Action\Position\CreatePositionAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreatePositionController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/positions', name: 'api.positions.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreatePositionAction $createPositionAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::CREATE, AccessEnum::POSITION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $createPositionAction->execute($createDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('position.add.success', [], 'positions')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('position.add.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
