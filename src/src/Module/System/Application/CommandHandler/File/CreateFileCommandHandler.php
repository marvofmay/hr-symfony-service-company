<?php

declare(strict_types=1);

namespace App\Module\System\Application\CommandHandler\File;

use App\Module\System\Application\Command\File\CreateFileCommand;
use App\Module\System\Domain\Service\File\FileCreator;

readonly class CreateFileCommandHandler
{
    public function __construct(private FileCreator $fileCreator,)
    {
    }

    public function __invoke(CreateFileCommand $command): void
    {
        $this->fileCreator->create($command->file);
    }
}
