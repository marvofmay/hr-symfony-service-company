<?php

declare(strict_types=1);

namespace App\Common\Presentation\Action;

use App\Common\Application\Command\UploadFileCommand;
use App\Common\Domain\DTO\UploadFileDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UploadFileAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(UploadFileDTO $uploadFileDTO): void
    {
        $this->commandBus->dispatch(new UploadFileCommand($uploadFileDTO->file, $uploadFileDTO->uploadFilePath, $uploadFileDTO->uploadFileName));
    }
}
