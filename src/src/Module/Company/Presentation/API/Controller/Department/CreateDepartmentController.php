<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Module\Company\Domain\DTO\Department\CreateDTO;
use App\Module\Company\Presentation\API\Action\Department\CreateDepartmentAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateDepartmentController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/departments', name: 'api.departments.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateDepartmentAction $createDepartmentAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::CREATE, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $createDepartmentAction->execute($createDTO);

            return new JsonResponse(['message' => $this->translator->trans('department.add.success', [], 'departments')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('department.add.error', [], 'departments'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
