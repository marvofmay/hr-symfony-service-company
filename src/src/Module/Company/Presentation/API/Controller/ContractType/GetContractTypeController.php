<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Module\Company\Application\Transformer\ContractType\ContractTypeDataTransformer;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetContractTypeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/contract_types/{uuid}', name: 'api.contract_types.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::VIEW, AccessEnum::CONTRACT_TYPE)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $contractType =  $this->contractTypeReaderRepository->getContractTypeByUUID($uuid);
            $transformer = new ContractTypeDataTransformer();
            $data = $transformer->transformToArray($contractType);

            return new JsonResponse(['data' => $data,], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('contractType.view.error', [], 'contract_types'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
