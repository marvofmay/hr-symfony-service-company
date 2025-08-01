<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\Company\ImportDTO;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsValidator;
use App\Module\Company\Presentation\API\Action\Department\ImportDepartmentsAction;
use App\Module\System\Application\Transformer\File\UploadFileErrorTransformer;
use App\Module\System\Application\Transformer\ImportLog\ImportLogErrorTransformer;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\File\AskFileAction;
use App\Module\System\Presentation\API\Action\File\CreateFileAction;
use App\Module\System\Presentation\API\Action\Import\AskImportAction;
use App\Module\System\Presentation\API\Action\Import\CreateImportAction;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use App\Module\System\Presentation\API\Action\ImportLog\AskImportLogsAction;
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

class ImportDepartmentsController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/departments/import', name: 'import', methods: ['POST'])]
    public function import(
        #[MapUploadedFile] UploadedFile $file,
        UploadFileAction $uploadFileAction,
        ImportDepartmentsAction $importDepartmentsAction,
        ImportDepartmentsValidator $importDepartmentsValidator,
        CreateFileAction $createFileAction,
        AskFileAction $askFileAction,
        CreateImportAction $createImportAction,
        UpdateImportAction $updateImportAction,
        ImportLogMultipleCreator $importLogMultipleCreator,
        AskImportAction $askImportAction,
        AskImportLogsAction $askImportLogsAction,
        ValidatorInterface $validator,
        Security $security,
        ParameterBagInterface $params,
    ): JsonResponse {
        try {
            if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::DEPARTMENT)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }
            $employee = $security->getUser()->getEmployee();

            $uploadFilePath = sprintf('%s/departments', $params->get('upload_file_path'));
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                return new JsonResponse(['message' => $this->translator->trans('department.import.error', [], 'departments'), 'errors' => UploadFileErrorTransformer::map($errors)], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $uploadFileAction->execute($uploadFileDTO);
            $createFileAction->execute($fileName, $uploadFilePath, $employee);
            $file = $askFileAction->ask($fileName, $uploadFilePath, FileKindEnum::IMPORT_XLSX);
            $createImportAction->execute(ImportKindEnum::IMPORT_DEPARTMENTS, ImportStatusEnum::PENDING, $file, $employee);
            $import = $askImportAction->ask($file);

            $errors = $importDepartmentsValidator->validate($import);
            if (!empty($errors)) {
                $updateImportAction->execute($import, ImportStatusEnum::FAILED);
                $importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);

                foreach ($errors as $error) {
                    $this->logger->error($this->translator->trans('department.import.error', [], 'departments').': '.$error);
                }

                $importLogs = $askImportLogsAction->ask($import);
                $mappedErrors = ImportLogErrorTransformer::map($importLogs);

                return new JsonResponse([
                    'message' => $this->translator->trans('department.import.error', [], 'departments'),
                    'errors' => $mappedErrors,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $importDepartmentsAction->execute(new ImportDTO($import->getUUID()->toString()));

            return new JsonResponse(['message' => $this->translator->trans('department.import.queued', [], 'departments'), 'errors' => []], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('department.import.error', [], 'departments'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message, 'errors' => []], $error->getCode());
        }
    }
}
