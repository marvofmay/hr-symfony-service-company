<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\File;

use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Module\Company\Domain\Entity\User;
use App\Module\System\Application\Command\File\CreateFileCommand;
use App\Module\System\Domain\Entity\File;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateFileAction
{
    public function __construct(#[Autowire(service: 'command.bus')] private MessageBusInterface $commandBus)
    {
    }

    public function execute(string $fileName, string $filePath, User $user): void
    {
        $this->commandBus->dispatch(new CreateFileCommand(File::create($fileName, $filePath, FileExtensionEnum::XLSX, FileKindEnum::IMPORT_XLSX, $user)));
    }
}
