<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Module\Company\Domain\DTO\Company\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Company\UpdateCompanyAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateCompanyController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator,)
    {
    }

    #[Route('/api/companies/{uuid}', name: 'api.company.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateCompanyAction $updateCompanyAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::UPDATE, AccessEnum::COMPANY)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            if ($uuid !== $updateDTO->getUUID()) {
                throw new \Exception($this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators'), Response::HTTP_CONFLICT);
            }

            $updateCompanyAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('company.update.success', [], 'companies')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('company.update.error', [], 'companies'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
