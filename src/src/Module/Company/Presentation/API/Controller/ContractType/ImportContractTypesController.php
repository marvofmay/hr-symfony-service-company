<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\ContractType\ImportDTO;
use App\Module\Company\Presentation\API\Action\ContractType\ImportContractTypesAction;
use App\Module\System\Application\Transformer\File\UploadFileErrorTransformer;
use App\Module\System\Application\Transformer\ImportLog\ImportLogErrorTransformer;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use App\Module\System\Presentation\API\Action\File\AskFileAction;
use App\Module\System\Presentation\API\Action\File\CreateFileAction;
use App\Module\System\Presentation\API\Action\Import\AskImportAction;
use App\Module\System\Presentation\API\Action\Import\CreateImportAction;
use App\Module\System\Presentation\API\Action\ImportLog\AskImportLogsAction;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportContractTypesController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface        $logger,
        private readonly TranslatorInterface    $translator,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/api/contract_types/import', name: 'import', methods: ['POST'])]
    public function import(
        #[MapUploadedFile] UploadedFile $file,
        UploadFileAction                $uploadFileAction,
        ImportContractTypesAction       $importContractTypesAction,
        CreateFileAction                $createFileAction,
        AskFileAction                   $askFileAction,
        CreateImportAction              $createImportAction,
        AskImportAction                 $askImportAction,
        AskImportLogsAction             $askImportLogsAction,
        ValidatorInterface              $validator,
        Security                        $security,
        ParameterBagInterface           $params,
    ): JsonResponse
    {
        $this->entityManager->beginTransaction();
        try {
            if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::CONTRACT_TYPE)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }
            $employee = $security->getUser()->getEmployee();

            $uploadFilePath = sprintf('%s/contract_types', $params->get('upload_file_path'));
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                return new JsonResponse(['message' => $this->translator->trans('contractType.import.error', [], 'contract_types'), 'errors' => UploadFileErrorTransformer::map($errors)], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $uploadFileAction->execute($uploadFileDTO);
            $createFileAction->execute($fileName, $uploadFilePath, $employee);
            $file = $askFileAction->ask($fileName, $uploadFilePath, FileKindEnum::IMPORT_XLSX);
            $createImportAction->execute(ImportKindEnum::IMPORT_CONTRACT_TYPES, ImportStatusEnum::PENDING, $file, $employee);
            $import = $askImportAction->ask($file);
            $importContractTypesAction->execute(new ImportDTO($import->getUUID()->toString()));

            $this->entityManager->commit();

            if ($import->getStatus() === ImportStatusEnum::DONE) {
                return new JsonResponse(['message' => $this->translator->trans('contractType.import.success', [], 'contract_types'), 'errors' => [],], Response::HTTP_CREATED);
            } else {
                $importLogs = $askImportLogsAction->ask($import);
                $errors = ImportLogErrorTransformer::map($importLogs);

                return new JsonResponse(['message' => $this->translator->trans('contractType.import.error', [], 'contract_types'), 'errors' => $errors,], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $error) {
            $this->entityManager->rollback();
            $message = sprintf('%s. %s', $this->translator->trans('contractType.import.error', [], 'contract_types'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message, 'errors' => []], $error->getCode());
        }
    }
}
