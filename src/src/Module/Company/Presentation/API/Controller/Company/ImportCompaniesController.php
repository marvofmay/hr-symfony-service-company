<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Facade\ImportCompaniesFacade;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class ImportCompaniesController extends AbstractController
{
    public function __construct(private readonly ImportCompaniesFacade $importCompaniesFacade, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/companies/import', name: 'import', methods: ['POST'])]
    public function import(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionEnum::IMPORT,
            AccessEnum::COMPANY,
            $this->messageService->get('accessDenied')
        );

        if (!$file) {
            return new JsonResponse(
                ['message' => $this->messageService->get('company.import.file.required', [], 'companies')],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $result = $this->importCompaniesFacade->handle($file);

        $responseData = ['message' => $result['message']];
        if (!empty($result['errors'])) {
            $responseData['errors'] = $result['errors'];
        }

        return new JsonResponse($responseData, $result['success'] ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
