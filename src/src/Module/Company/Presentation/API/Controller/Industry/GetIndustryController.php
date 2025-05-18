<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Module\Company\Application\Transformer\Industry\IndustryDataTransformer;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetIndustryController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly IndustryReaderInterface $industryReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/industries/{uuid}', name: 'api.industries.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::VIEW, AccessEnum::INDUSTRY)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $industry = $this->industryReaderRepository->getIndustryByUUID($uuid);
            $transformer = new IndustryDataTransformer();
            $data = $transformer->transformToArray($industry);

            return new JsonResponse(['data' => $data,], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('industry.view.error', [], 'industries'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
