<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Common\UploadFile\UploadFile;
use App\Module\Company\Domain\DTO\Position\ImportDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\ImportPositionsFromXLSX;
use App\Module\Company\Presentation\API\Action\Position\ImportPositionsAction;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportPositionsController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
    ) {
    }

    #[OA\Post(
        path: '/api/positions/import',
        summary: 'Importuje nowe stanowiska',
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
                description: 'Stanowiska zostały utworzone',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Stanowiska zostały pomyślnie zaimportowane'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Błąd importu',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Wystąpił błąd - stanowiska nie zostały zaimportowane'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'positions')]
    #[Route('/api/positions/import', name: 'import', methods: ['POST'])]
    public function import(Request $request, ImportPositionsAction $importPositionsAction): JsonResponse
    {
        try {
            $uploadFilePath = '../src/Storage/Upload/Import/Positions';
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                return new JsonResponse(
                    ['errors' => [$this->translator->trans('file.chooseFile', [], 'validators')]],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileService = new UploadFile($uploadFilePath, 'xlsx');
            $uploadFileService->uploadFile($uploadedFile);

            $importer = new ImportPositionsFromXLSX(
                sprintf('%s/%s', $uploadFilePath, $uploadFileService->getFileName()),
                $this->translator,
                $this->positionReaderRepository,
                $this->departmentReaderRepository
            );

            $importer->import();
            $errors = $importer->getErrors();

            if (empty($errors)) {
                $data = $importer->groupPositions();
                $importPositionsAction->execute(new ImportDTO($data));

                return new JsonResponse([
                    'success' => empty($importer->getErrors()),
                    'message' => $this->translator->trans('position.import.success', [], 'positions'),
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
            $message = sprintf('%s: %s', $this->translator->trans('position.import.error', [], 'positions'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
