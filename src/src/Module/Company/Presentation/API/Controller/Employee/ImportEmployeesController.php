<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Employee;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\Employee\ImportDTO;
use App\Module\Company\Presentation\API\Action\Employee\ImportEmployeesAction;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Presentation\API\Action\File\AskFileAction;
use App\Module\System\Presentation\API\Action\File\CreateFileAction;
use App\Module\System\Presentation\API\Action\Import\AskImportAction;
use App\Module\System\Presentation\API\Action\Import\CreateImportAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportEmployeesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[OA\Post(
        path: '/api/employees/import',
        summary: 'Importuje nowych pracowników oraz aktualizuje istniejących - obsługa przez kolejkę RabbitMQ',
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
                description: 'Pracownicy zostali utworzeni / zaktualizowani',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Pracownicy zostali pomyślnie zaimportowani'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd importu',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Wystąpił błąd - pracownicy nie zostali zaimportowani'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'employees')]
    #[Route('/api/employees/import', name: 'import', methods: ['POST'])]
    public function import(
        #[MapUploadedFile] UploadedFile $file,
        ValidatorInterface $validator,
        UploadFileAction $uploadFileAction,
        ImportEmployeesAction $importEmployeesAction,
        CreateFileAction $createFileAction,
        AskFileAction $askFileAction,
        CreateImportAction $createImportAction,
        AskImportAction $askImportAction,
        Security $security,
    ): JsonResponse {
        try {
            $uploadFilePath = 'src/Storage/Upload/Import/Employees';
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);
            $employee = $security->getUser()->getEmployee();

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                return new JsonResponse(
                    ['errors' => array_map(fn ($e) => [
                        'field' => $e->getPropertyPath(),
                        'message' => $e->getMessage()], iterator_to_array($errors))
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileAction->execute($uploadFileDTO);
            $createFileAction->execute($fileName, $uploadFilePath, $employee);
            $file = $askFileAction->ask($fileName, $uploadFilePath, FileKindEnum::IMPORT_XLSX);
            $createImportAction->execute(ImportKindEnum::IMPORT_EMPLOYEES, ImportStatusEnum::PENDING, $file, $employee);
            $import = $askImportAction->ask($file);
            $importEmployeesAction->execute(new ImportDTO($import->getUUID()->toString()));

            return new JsonResponse([
                'message' => $this->translator->trans('employee.import.queued', [], 'employees'),
            ],
                Response::HTTP_OK
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('employee.import.error', [], 'employees'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
