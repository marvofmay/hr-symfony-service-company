<?php

namespace App\Module\Company\Application\Facade;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\Role\ImportDTO;
use App\Module\Company\Presentation\API\Action\Role\ImportRolesAction;
use App\Module\System\Application\Event\LogFileEvent;
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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ImportRolesFacade
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UploadFileAction $uploadFileAction,
        private ImportRolesAction $importRolesAction,
        private CreateFileAction $createFileAction,
        private AskFileAction $askFileAction,
        private CreateImportAction $createImportAction,
        private AskImportAction $askImportAction,
        private AskImportLogsAction $askImportLogsAction,
        private ValidatorInterface $validator,
        private Security $security,
        private ParameterBagInterface $params,
        private MessageService $messageService,
        private MessageBusInterface $eventBus,
    ) {
    }

    public function handle(UploadedFile $file): array
    {
        $this->entityManager->beginTransaction();
        try {
            $employee = $this->security->getUser()->getEmployee();
            $uploadFilePath = sprintf('%s/roles', $this->params->get('upload_file_path'));
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $this->validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                $message = $this->messageService->get('role.import.error', [], 'roles');

                return [
                    'success' => false,
                    'errors' => UploadFileErrorTransformer::map($errors),
                    'message' => $message,
                ];
            }

            $this->uploadFileAction->execute($uploadFileDTO);
            $this->createFileAction->execute($fileName, $uploadFilePath, $employee);
            $file = $this->askFileAction->ask($fileName, $uploadFilePath, FileKindEnum::IMPORT_XLSX);
            $this->createImportAction->execute(ImportKindEnum::IMPORT_ROLES, ImportStatusEnum::PENDING, $file, $employee);
            $import = $this->askImportAction->ask($file);
            $this->importRolesAction->execute(new ImportDTO($import->getUUID()->toString()));

            $this->entityManager->commit();

            if (ImportStatusEnum::DONE === $import->getStatus()) {
                $message = $this->messageService->get('role.import.success', [], 'roles');

                return [
                    'success' => true,
                    'message' => $message,
                ];
            }

            $importLogs = $this->askImportLogsAction->ask($import);
            $message = $this->messageService->get('role.import.error', [], 'roles');

            return [
                'success' => false,
                'errors' => ImportLogErrorTransformer::map($importLogs),
                'message' => $message,
            ];
        } catch (\Exception $error) {
            $this->entityManager->rollback();
            $message = sprintf('%s. %s', $this->messageService->get('role.import.error', [], 'roles'), $this->messageService->get($error->getMessage()));
            $this->eventBus->dispatch(new LogFileEvent($message));

            return [
                'success' => false,
                'message' => $message,
                'errors' => [],
            ];
        }
    }
}
