<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Module\Company\Domain\DTO\Position\ImportDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\ImportPositionsFromXLSX;
use App\Module\Company\Presentation\API\Action\Position\ImportPositionsAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
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

    #[Route('/api/positions/import', name: 'import', methods: ['POST'])]
    public function import(Request $request, ImportPositionsAction $importPositionsAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::POSITION)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $uploadFilePath = '../src/Storage/Upload/Import/Positions';
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                return new JsonResponse(
                    ['errors' => [$this->translator->trans('file.chooseFile', [], 'validators')]],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileService = new UploadFile($uploadFilePath, FileExtensionEnum::XLSX);
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
            $message = sprintf('%s. %s', $this->translator->trans('position.import.error', [], 'positions'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
