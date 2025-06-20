<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Module\Company\Application\Transformer\Department\DepartmentDataTransformer;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetDepartmentController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/departments/{uuid}', name: 'api.departments.get', methods: ['GET'])]
    public function get(string $uuid): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::VIEW, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $transformer = new DepartmentDataTransformer();
            $department = $this->departmentReaderRepository->getDepartmentByUUID($uuid);
            $data = $transformer->transformToArray($department);

            return new JsonResponse(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('department.view.error', [], 'departments'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
