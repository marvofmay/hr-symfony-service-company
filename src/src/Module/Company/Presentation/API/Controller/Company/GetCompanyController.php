<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Module\Company\Application\Transformer\Company\CompanyDataTransformer;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetCompanyController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/companies/{uuid}', name: 'api.companies.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::VIEW, AccessEnum::COMPANY)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $company = $this->companyReaderRepository->getCompanyByUUID($uuid);
            $transformer = new CompanyDataTransformer();
            $data = $transformer->transformToArray($company);

            return new JsonResponse(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('company.view.error', [], 'companies'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
