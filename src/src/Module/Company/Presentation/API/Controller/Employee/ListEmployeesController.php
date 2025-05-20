<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Module\Company\Domain\DTO\Employee\EmployeesQueryDTO;
use App\Module\Company\Presentation\API\Action\Employee\AskEmployeesAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListEmployeesController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator,) {
    }

    #[Route('/api/employees', name: 'api.employees.list', methods: ['GET'])]
    public function list(#[MapQueryString] EmployeesQueryDTO $queryDTO, AskEmployeesAction $askEmployeesAction): Response
    {
        try {
            if (!$this->isGranted(PermissionEnum::LIST, AccessEnum::EMPLOYEE)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            return new JsonResponse(['data' => $askEmployeesAction->ask($queryDTO)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('employee.list.error', [], 'employees'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $this->translator->trans('employee.list.error', [], 'employees'),], $error->getCode());
        }
    }
}
