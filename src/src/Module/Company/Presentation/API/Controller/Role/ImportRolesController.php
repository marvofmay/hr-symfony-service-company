<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\Role\ImportDTO;
use App\Module\Company\Presentation\API\Action\Role\ImportRolesAction;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Presentation\API\Action\File\AskFileAction;
use App\Module\System\Presentation\API\Action\File\CreateFileAction;
use App\Module\System\Presentation\API\Action\Import\AskImportAction;
use App\Module\System\Presentation\API\Action\Import\CreateImportAction;
use Doctrine\ORM\EntityManagerInterface;
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

class ImportRolesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly ImportReaderInterface $importReaderRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[OA\Post(
        path: '/api/roles/import',
        summary: 'Importuje nowe role',
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
                response: Response::HTTP_CREATED,
                description: 'Role zostały utworzone',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Role zostały pomyślnie zaimportowane'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd importu',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Wystąpił błąd - role nie zostały zaimportowane'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'roles')]
    #[Route('/api/roles/import', name: 'import', methods: ['POST'])]
    public function import(
        #[MapUploadedFile] UploadedFile $file,
        UploadFileAction $uploadFileAction,
        ImportRolesAction $importRolesAction,
        CreateFileAction $createFileAction,
        AskFileAction $askFileAction,
        CreateImportAction $createImportAction,
        AskImportAction $askImportAction,
        ValidatorInterface $validator,
        Security $security,
    ): JsonResponse {

        $this->entityManager->beginTransaction();
        try {
            $uploadFilePath = 'src/Storage/Upload/Import/Roles';
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
            $createImportAction->execute(ImportKindEnum::IMPORT_ROLES, ImportStatusEnum::PENDING, $file, $employee);
            $import = $askImportAction->ask($file);
            $importRolesAction->execute(new ImportDTO($import->getUUID()->toString()));

            $this->entityManager->commit();

            if ($import->getStatus() === ImportStatusEnum::DONE) {
                return new JsonResponse([
                    'success' => empty($errors),
                    'message' => $this->translator->trans('role.import.success', [], 'roles'),
                    'errors' => [],
                ],
                    Response::HTTP_CREATED
                );
            } else {
                //ToDo:: $askImportLogAction
                //$import = $askImportLogAction->ask($import->getUUID()->toString());
                //$importLogs = $import->getImportLogs();
                return new JsonResponse([
                    'errors' => 'errorsArray',
                ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        } catch (\Exception $error) {
            $this->entityManager->rollback();
            $message = sprintf('%s: %s', $this->translator->trans('role.import.error', [], 'roles'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
