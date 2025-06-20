<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Domain\DTO\Position\DeleteMultipleDTO;
use App\Module\Company\Presentation\API\Action\Position\DeleteMultiplePositionsAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DeleteMultiplePositionsController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/positions/multiple', name: 'api.positions.delete_multiple', methods: ['DELETE'])]
    public function delete(
        #[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO,
        DeleteMultiplePositionsAction $deleteMultiplePositionsAction,
    ): JsonResponse {
        try {
            if (!$this->isGranted(PermissionEnum::DELETE, AccessEnum::POSITION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $deleteMultiplePositionsAction->execute($deleteMultipleDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('position.delete.multiple.success', [], 'positions')],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('position.delete.multiple.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
