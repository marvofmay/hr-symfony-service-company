<?php

namespace App\Module\Company\Application\Facade;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\Industry\ImportDTO;
use App\Module\Company\Presentation\API\Action\Industry\ImportIndustriesAction;
use App\Module\System\Application\Transformer\File\UploadFileErrorTransformer;
use App\Module\System\Application\Transformer\ImportLog\ImportLogErrorTransformer;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Presentation\API\Action\File\AskFileAction;
use App\Module\System\Presentation\API\Action\File\CreateFileAction;
use App\Module\System\Presentation\API\Action\Import\AskImportAction;
use App\Module\System\Presentation\API\Action\Import\CreateImportAction;
use App\Module\System\Presentation\API\Action\ImportLog\AskImportLogsAction;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ImportIndustriesFacade
{
    public function __construct(
        private LoggerInterface $logger,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private UploadFileAction $uploadFileAction,
        private ImportIndustriesAction $importIndustriesAction,
        private CreateFileAction $createFileAction,
        private AskFileAction $askFileAction,
        private CreateImportAction $createImportAction,
        private AskImportAction $askImportAction,
        private AskImportLogsAction $askImportLogsAction,
        private ValidatorInterface $validator,
        private Security $security,
        private ParameterBagInterface $params,
    ) {}

    public function handle(UploadedFile $file): array
    {
        $this->entityManager->beginTransaction();
        try {
            $employee = $this->security->getUser()->getEmployee();
            $uploadFilePath = sprintf('%s/industries', $this->params->get('upload_file_path'));
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $this->validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                return [
                    'success' => false,
                    'errors' => UploadFileErrorTransformer::map($errors),
                    'message' => $this->translator->trans('industry.import.error', [], 'industries'),
                ];
            }

            $this->uploadFileAction->execute($uploadFileDTO);
            $this->createFileAction->execute($fileName, $uploadFilePath, $employee);
            $file = $this->askFileAction->ask($fileName, $uploadFilePath, FileKindEnum::IMPORT_XLSX);
            $this->createImportAction->execute(ImportKindEnum::IMPORT_INDUSTRIES, ImportStatusEnum::PENDING, $file, $employee);
            $import = $this->askImportAction->ask($file);
            $this->importIndustriesAction->execute(new ImportDTO($import->getUUID()->toString()));

            $this->entityManager->commit();

            if ($import->getStatus() === ImportStatusEnum::DONE) {
                return [
                    'success' => true,
                    'message' => $this->translator->trans('industry.import.success', [], 'industries'),
                ];
            }

            $importLogs = $this->askImportLogsAction->ask($import);
            return [
                'success' => false,
                'errors' => ImportLogErrorTransformer::map($importLogs),
                'message' => $this->translator->trans('industry.import.error', [], 'industries'),
            ];
        } catch (\Exception $error) {
            $this->entityManager->rollback();
            $message = sprintf('%s. %s', $this->translator->trans('industry.import.error', [], 'industries'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return [
                'success' => false,
                'message' => $message,
                'errors' => [],
            ];
        }
    }
}
