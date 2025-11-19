<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Facade\ImportEmployeesFacade;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class ImportEmployeesController extends AbstractController
{
    public function __construct(
        private readonly ImportEmployeesFacade $importEmployeesFacade,
        private readonly MessageService $messageService
    )
    {
    }

    #[Route('/api/employees/import', name: 'api.employees.import', methods: ['POST'])]
    public function import(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionEnum::IMPORT,
            AccessEnum::EMPLOYEE,
            $this->messageService->get('accessDenied')
        );

        if (!$file) {
            return new JsonResponse(
                ['message' => $this->messageService->get('employee.import.file.required', [], 'employees')],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->importEmployeesFacade->enqueue($file);

        return new JsonResponse([
            'success' => true,
            'message' => $this->messageService->get('employee.import.enqueued', [], 'employees'),
        ], Response::HTTP_ACCEPTED);
    }
}
