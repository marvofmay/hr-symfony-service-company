<?php

declare(strict_types=1);

namespace App\Common\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileCommand
{
    public function __construct(public UploadedFile $file, public string $uploadFilePath, public string $uploadFileName,)
    {
    }
}
