<?php

namespace App\Module\Company\Application\Facade;

use App\Common\Application\Command\UploadFileCommand;
use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Module\Company\Application\Command\Employee\ImportEmployeesCommand;
use App\Module\System\Application\Command\File\CreateFileCommand;
use App\Module\System\Application\Command\Import\CreateImportCommand;
use App\Module\System\Application\Query\File\GetFileByNamePathAndKindQuery;
use App\Module\System\Application\Query\Import\GetImportByFileQuery;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ImportEmployeesFacade
{
    public function __construct(
        private ValidatorInterface $validator,
        private Security $security,
        private ParameterBagInterface $params,
        #[Autowire(service: 'command.bus')] private MessageBusInterface $commandBus,
        #[Autowire(service: 'query.bus')] private MessageBusInterface $queryBus,
    ) {
    }

    public function enqueue(UploadedFile $file): void
    {
        $user = $this->security->getUser();
        $uploadFilePath = sprintf('%s/employees', $this->params->get('upload_file_path'));

        $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);
        $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);

        $errors = $this->validator->validate($uploadFileDTO);

        if (count($errors) > 0) {
            throw new \DomainException("Invalid file");
        }

        $this->commandBus->dispatch(new UploadFileCommand($uploadFileDTO->file, $uploadFileDTO->uploadFilePath, $uploadFileDTO->uploadFileName));

        $this->commandBus->dispatch(
            new CreateFileCommand(
                File::create(
                    fileName: $fileName,
                    filePath: $uploadFilePath,
                    fileExtension: FileExtensionEnum::XLSX,
                    fileKind: FileKindEnum::IMPORT_XLSX,
                    user: $user
                )
            )
        );

        $fileEntity = $this->queryBus
            ->dispatch(new GetFileByNamePathAndKindQuery($fileName, $uploadFilePath, FileKindEnum::IMPORT_XLSX))
            ->last(HandledStamp::class)->getResult();

        $this->commandBus->dispatch(
            new CreateImportCommand(
                kindEnum: ImportKindEnum::IMPORT_EMPLOYEES,
                statusEnum: ImportStatusEnum::PENDING,
                file: $fileEntity,
                user: $user
            )
        );

        $import = $this->queryBus
            ->dispatch(new GetImportByFileQuery($fileEntity))
            ->last(HandledStamp::class)->getResult();


        $this->commandBus->dispatch(
            new ImportEmployeesCommand(
                importUUID: $import->getUUID()->toString(),
                loggedUserUUID: $user->getUUID()->toString()
            )
        );
    }
}
