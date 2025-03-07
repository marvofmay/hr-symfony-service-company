<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Employee;

readonly class ImportEmployeesCommand
{
    public function __construct(private ?string $uploadFilePath, private ?string $fileName,)
    {
    }

    public function getUploadFilePath(): ?string
    {
        return $this->uploadFilePath;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }
}
