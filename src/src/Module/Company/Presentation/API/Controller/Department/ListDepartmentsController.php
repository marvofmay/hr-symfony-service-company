<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Module\Company\Domain\DTO\Department\DepartmentsQueryDTO;
use App\Module\Company\Presentation\API\Action\Department\AskDepartmentsAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListDepartmentsController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/departments', name: 'api.departments.list', methods: ['GET'])]
    public function list(#[MapQueryString] DepartmentsQueryDTO $queryDTO, AskDepartmentsAction $askDepartmentsAction): Response
    {
        try {
            if (!$this->isGranted(PermissionEnum::LIST, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            return new JsonResponse(['data' => $askDepartmentsAction->ask($queryDTO)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('department.list.error', [], 'departments'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
