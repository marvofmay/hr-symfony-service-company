<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Domain\DTO\Position\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Position\UpdatePositionAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdatePositionController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/api/positions/{uuid}', name: 'api.positions.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdatePositionAction $updatePositionAction): Response
    {
        try {
            if (!$this->isGranted(PermissionEnum::UPDATE, AccessEnum::POSITION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            if ($uuid !== $updateDTO->getUUID()) {
                throw new \Exception($this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators'), Response::HTTP_CONFLICT);
            }

            $updatePositionAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('position.update.success', [], 'positions')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('position.update.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
