<?php

namespace App\Module\Company\Application\Facade;

use _PHPStan_6597ef616\Psr\Log\LogLevel;
use App\Common\Application\Command\UploadFileCommand;
use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\System\Application\Command\File\CreateFileCommand;
use App\Module\System\Application\Command\Import\CreateImportCommand;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Application\Query\File\GetFileByNamePathAndKindQuery;
use App\Module\System\Application\Query\Import\GetImportByFileQuery;
use App\Module\System\Application\Query\ImportLog\GetImportLogsByImportQuery;
use App\Module\System\Application\Transformer\File\UploadFileErrorTransformer;
use App\Module\System\Application\Transformer\ImportLog\ImportLogErrorTransformer;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ImportRolesFacade
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private Security $security,
        private ParameterBagInterface $params,
        private MessageService $messageService,
        #[Autowire(service: 'event.bus')] private MessageBusInterface $eventBus,
        #[Autowire(service: 'command.bus')] private MessageBusInterface $commandBus,
        #[Autowire(service: 'query.bus')] private MessageBusInterface $queryBus,
    ) {
    }

    public function handle(UploadedFile $file): array
    {
        $this->entityManager->beginTransaction();
        try {
            $user = $this->security->getUser();
            $uploadFilePath = sprintf('%s/roles', $this->params->get('upload_file_path'));
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $this->validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                $message = $this->messageService->get('role.import.error', [], 'roles');

                return [
                    'success' => false,
                    'errors'  => UploadFileErrorTransformer::map($errors),
                    'message' => $message,
                ];
            }

            try {
                $this->commandBus->dispatch(new UploadFileCommand(file: $uploadFileDTO->file, uploadFilePath: $uploadFileDTO->uploadFilePath, uploadFileName: $uploadFileDTO->uploadFileName));
                $this->commandBus->dispatch(new CreateFileCommand(File::create(fileName: $fileName, filePath: $uploadFilePath, fileExtension: FileExtensionEnum::XLSX, fileKind: FileKindEnum::IMPORT_XLSX, user: $user)));
                $handleStamp = $this->queryBus->dispatch(new GetFileByNamePathAndKindQuery(fileName: $fileName, filePath: $uploadFilePath, fileKind: FileKindEnum::IMPORT_XLSX));
                $file = $handleStamp->last(HandledStamp::class)->getResult();
                $this->commandBus->dispatch(new CreateImportCommand(kindEnum: ImportKindEnum::IMPORT_ROLES, statusEnum: ImportStatusEnum::PENDING, file: $file, user: $user));
                $handleStamp = $this->queryBus->dispatch(new GetImportByFileQuery($file));
                $import = $handleStamp->last(HandledStamp::class)->getResult();
                $this->commandBus->dispatch(new ImportRolesCommand($import->getUUID()->toString()));
            } catch (HandlerFailedException $exception) {
                throw $exception->getPrevious();
            }

            $this->entityManager->commit();

            if (ImportStatusEnum::DONE === $import->getStatus()) {
                $message = $this->messageService->get('role.import.success', [], 'roles');

                return [
                    'success' => true,
                    'message' => $message,
                ];
            }

            $handleStamp = $this->queryBus->dispatch(new GetImportLogsByImportQuery($import));
            $importLogs =  $handleStamp->last(HandledStamp::class)->getResult();
            $message = $this->messageService->get('role.import.error', [], 'roles');

            return [
                'success' => false,
                'errors'  => ImportLogErrorTransformer::map($importLogs),
                'message' => $message,
            ];
        } catch (\Exception $error) {
            $this->entityManager->rollback();
            $message = sprintf('%s %s', $this->messageService->get('role.import.error', [], 'roles'), $this->messageService->get($error->getMessage()));
            $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR, MonologChanelEnum::IMPORT));

            return [
                'success' => false,
                'message' => $message,
                'errors'  => [],
            ];
        }
    }
}
