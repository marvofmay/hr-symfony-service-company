<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Module\Company\Domain\DTO\Department\DeleteMultipleDTO;
use App\Module\Company\Presentation\API\Action\Department\DeleteMultipleDepartmentsAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteMultipleDepartmentsController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/departments/multiple', name: 'api.departments.delete_multiple', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO, DeleteMultipleDepartmentsAction $deleteMultipleDepartmentsAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::DELETE, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $deleteMultipleDepartmentsAction->execute($deleteMultipleDTO);

            return new JsonResponse(['message' => $this->translator->trans('department.delete.multiple.success', [], 'departments')], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('department.delete.multiple.error', [], 'departments'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
