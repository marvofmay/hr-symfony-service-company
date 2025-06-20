<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Module\Company\Domain\DTO\Department\UpdateDTO;
use App\Module\Company\Presentation\API\Action\Department\UpdateDepartmentAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateDepartmentController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/departments/{uuid}', name: 'api.department.update', methods: ['PUT'])]
    public function update(string $uuid, #[MapRequestPayload] UpdateDTO $updateDTO, UpdateDepartmentAction $updateDepartmentAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::UPDATE, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            if ($uuid !== $updateDTO->getUUID()) {
                throw new \Exception($this->translator->trans('uuid.differentUUIDInBodyRawAndUrl', [], 'validators'), Response::HTTP_CONFLICT);
            }

            $updateDepartmentAction->execute($updateDTO);

            return new JsonResponse(['message' => $this->translator->trans('department.update.success', [], 'departments')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('department.update.error', [], 'departments'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
