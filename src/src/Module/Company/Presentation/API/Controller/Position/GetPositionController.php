<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Module\Company\Application\Transformer\Position\PositionDataTransformer;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetPositionController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/positions/{uuid}', name: 'api.positions.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::VIEW, AccessEnum::POSITION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $position =  $this->positionReaderRepository->getPositionByUUID($uuid);
            $transformer = new PositionDataTransformer();
            $data = $transformer->transformToArray($position);

            return new JsonResponse(['data' => $data,], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('position.view.error', [], 'positions'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
