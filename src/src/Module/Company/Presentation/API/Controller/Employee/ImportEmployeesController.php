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

    #[Route('/api/employees/import', name: 'import', methods: ['POST'])]
    public function import(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::EMPLOYEE)) {
            return new JsonResponse(['message' => $this->messageService->get('accessDenied')], Response::HTTP_FORBIDDEN);
        }

        if (!$file) {
            return new JsonResponse(
                ['message' => $this->messageService->get('employee.import.file.required', [], 'employees')],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $result = $this->importEmployeesFacade->handle($file);

        $responseData = ['message' => $result['message']];
        if (!empty($result['errors'])) {
            $responseData['errors'] = $result['errors'];
        }

        return new JsonResponse($responseData, $result['success'] ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
