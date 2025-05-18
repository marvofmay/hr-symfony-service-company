<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Module\Company\Domain\DTO\Industry\ImportDTO;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Industry\ImportIndustriesFromXLSX;
use App\Module\Company\Presentation\API\Action\Industry\ImportIndustriesAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportIndustriesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly IndustryReaderInterface $industryReaderRepository,
    ) {
    }

    #[Route('/api/industries/import', name: 'import', methods: ['POST'])]
    public function import(Request $request, ImportIndustriesAction $importIndustriesAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::INDUSTRY)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $uploadFilePath = '../src/Storage/Upload/Import/Industries';
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                return new JsonResponse(
                    ['errors' => [$this->translator->trans('industry.import.fileRequired', [], 'industries')]],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileService = new UploadFile($uploadFilePath, FileExtensionEnum::XLSX);
            $uploadFileService->uploadFile($uploadedFile);

            $importer = new ImportIndustriesFromXLSX(
                sprintf('%s/%s', $uploadFilePath, $uploadFileService->getFileName()),
                $this->translator,
                $this->industryReaderRepository
            );

            $data = $importer->import();
            $errors = $importer->getErrors();

            if (empty($errors)) {
                $importIndustriesAction->execute(new ImportDTO($data));

                return new JsonResponse([
                    'success' => empty($importer->getErrors()),
                    'message' => $this->translator->trans('industry.import.success', [], 'industries'),
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
            $message = sprintf(
                '%s. %s',
                $this->translator->trans('industry.import.error', [], 'industries'),
                $this->translator->trans($error->getMessage())
            );
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
