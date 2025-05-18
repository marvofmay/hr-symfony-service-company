<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Module\Company\Domain\DTO\ContractType\ImportDTO;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Service\ContractType\ImportContractTypesFromXLSX;
use App\Module\Company\Presentation\API\Action\ContractType\ImportContractTypesAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportContractTypesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
    ) {
    }

    #[Route('/api/contract_types/import', name: 'import', methods: ['POST'])]
    public function import(Request $request, ImportContractTypesAction $importContractTypesAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::CONTRACT_TYPE)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $uploadFilePath = '../src/Storage/Upload/Import/ContractTypes';
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                return new JsonResponse(
                    ['errors' => [$this->translator->trans('file.chooseFile', [], 'validators')]],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileService = new UploadFile($uploadFilePath, FileExtensionEnum::XLSX);
            $uploadFileService->uploadFile($uploadedFile);

            $importer = new ImportContractTypesFromXLSX(
                sprintf('%s/%s', $uploadFilePath, $uploadFileService->getFileName()),
                $this->translator,
                $this->contractTypeReaderRepository
            );

            $data = $importer->import();
            $errors = $importer->getErrors();

            if (empty($errors)) {
                $importContractTypesAction->execute(new ImportDTO($data));

                return new JsonResponse([
                    'success' => empty($errors),
                    'message' => $this->translator->trans('contractType.import.success', [], 'contract_types'),
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
            $message = sprintf('%s. %s', $this->translator->trans('contractType.import.error', [], 'contract_types'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
