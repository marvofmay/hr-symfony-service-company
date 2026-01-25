<?php

declare(strict_types=1);

namespace App\Common\Application\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UploadFileDTO
{
    #[Assert\NotNull]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: [
            'text/csv',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'application/pdf',
        ]
    )]
    public UploadedFile $file;

    public string $uploadFilePath;
    public string $uploadFileName;

    public function __construct(UploadedFile $file, string $uploadFilePath, string $uploadFileName)
    {
        $this->file = $file;
        $this->uploadFilePath = $uploadFilePath;
        $this->uploadFileName = $uploadFileName;
    }
}
