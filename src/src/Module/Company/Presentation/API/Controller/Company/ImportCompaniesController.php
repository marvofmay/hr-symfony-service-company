<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Common\Enum\FileExtensionEnum;
use App\Common\UploadFile\UploadFile;
use App\Module\Company\Domain\DTO\Company\ImportDTO;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use App\Module\Company\Presentation\API\Action\Company\ImportCompaniesAction;
use Matrix\Exception;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportCompaniesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
//        private readonly CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    #[OA\Post(
        path: '/api/companies/import',
        summary: 'Importuje nowe firmy - obsługa przez kolejkę RabbitMQ',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(
                            property: 'file',
                            description: 'Plik XLSX do importu',
                            type: 'string',
                            format: 'binary'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Firmy zostały utworzone',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Firmy zostały pomyślnie zaimportowane'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd importu',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Wystąpił błąd - firmy nie zostały zaimportowane'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'companies')]
    #[Route('/api/companies/import', name: 'import', methods: ['POST'])]
    public function import(Request $request, ImportCompaniesAction $importCompaniesAction): JsonResponse
    {
        try {
            //Todo:: UploadFile::generateUniqueFileName
            $uploadedFile = $request->files->get('file');

            //ToDo:: make UploadFileDTO, actionUpload, UploadFileCommand, UploadFileCommandHandler
            if (!$uploadedFile) {
                return new JsonResponse(
                    ['errors' => [$this->translator->trans('company.import.fileRequired', [], 'companies')]],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFilePath = 'src/Storage/Upload/Import/Companies';
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileService = new UploadFile('../'.$uploadFilePath, FileExtensionEnum::XLSX, $fileName);
            $uploadFileService->uploadFile($uploadedFile);
            $fileName = $uploadFileService->getFileName();

            //ToDo:: save $uploadedFilePath, $fileName "import_log" table in feature
            //ToDO:: pass uuid to ImportDTO($uuid)
            $importCompaniesAction->execute(new ImportDTO($uploadFilePath, $fileName));

            return new JsonResponse([
                'message' => $this->translator->trans('company.import.queued', [], 'companies'),
            ],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('company.import.error', [], 'companies'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
