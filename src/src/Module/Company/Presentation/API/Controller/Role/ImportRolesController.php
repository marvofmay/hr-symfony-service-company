<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Module\Company\Domain\DTO\Role\ImportDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\ImportRolesFromXLSX;
use App\Module\Company\Presentation\API\Action\Role\ImportRolesAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportRolesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly RoleReaderInterface $roleReaderRepository,
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
    public function import(Request $request, ImportRolesAction $importRolesAction): JsonResponse
    {
        try {
            $uploadFilePath = '../src/Storage/Upload/Import/Roles';
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                return new JsonResponse(
                    ['errors' => [$this->translator->trans('file.chooseFile', [], 'validators')]],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileService = new UploadFile($uploadFilePath, 'xlsx');
            $uploadFileService->uploadFile($uploadedFile);

            $importer = new ImportRolesFromXLSX(
                sprintf('%s/%s', $uploadFilePath, $uploadFileService->getFileName()),
                $this->translator,
                $this->roleReaderRepository
            );

            $data = $importer->import();
            $errors = $importer->getErrors();

            if (empty($errors)) {
                $importRolesAction->execute(new ImportDTO($data));

                return new JsonResponse([
                    'success' => empty($errors),
                    'message' => $this->translator->trans('role.import.success', [], 'roles'),
                    'errors' => $importer->getErrors(),
                ],
                    Response::HTTP_CREATED
                );
            } else {
                return new JsonResponse([
                    'errors' => $importer->getErrors(),
                ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('role.import.error', [], 'roles'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
