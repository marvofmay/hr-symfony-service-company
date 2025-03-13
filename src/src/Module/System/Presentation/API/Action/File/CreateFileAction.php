<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\File;

use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Application\Command\File\CreateFileCommand;
use App\Module\System\Domain\Entity\File;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateFileAction
{
    public function __construct(private MessageBusInterface $commandBus,)
    {
    }

    public function execute(string $fileName, string $filePath, ?Employee $employee = null): void
    {
        $file = new File();
        $file->setFileName($fileName);
        $file->setFilePath($filePath);
        $file->setExtension(FileExtensionEnum::XLSX);
        $file->setKind(FileKindEnum::IMPORT_XLSX);
        $file->setEmployee($employee);

        $this->commandBus->dispatch(new CreateFileCommand($file));
    }
}