<?php

declare(strict_types=1);

namespace App\Common\UploadFile;

use App\Common\Enum\FileExtensionEnum;
use App\Common\Interface\UploadFileInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile implements UploadFileInterface
{
    private array $allowedExtensions = [
        FileExtensionEnum::PDF->value,
        FileExtensionEnum::CSV->value,
        FileExtensionEnum::PNG->value,
        FileExtensionEnum::XLSX->value,
        FileExtensionEnum::DOC->value,
        FileExtensionEnum::JPEG->value,
        FileExtensionEnum::JPG->value,
    ];

    private ?UploadedFile $uploadedFile = null;

    public function __construct(private readonly string $uploadDir, private readonly string $expectedUploadedFileExtension)
    {
    }

    public function uploadFile(UploadedFile $file): bool
    {
        if (!is_dir($this->uploadDir) && !mkdir($this->uploadDir, 0777, true) && !is_dir($this->uploadDir)) {
            return false;
        }

        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();

        if (!$this->isAllowedExtension($extension)) {
            throw new \Exception('notAllowedTypeFile');
        }

        if (!$this->isExpectedExtension($extension)) {
            switch ($this->expectedUploadedFileExtension) {
                case 'xlsx':
                    throw new \Exception('expectedXLSXFile');
                case 'pdf':
                    throw new \Exception('expectedPDFFile');
            }
        }

        $fileName = uniqid('upload_', true).'-'.date('Y-m-d-H-i-s').'.'.$extension;

        try {
            $movedFile = $file->move($this->uploadDir, $fileName);
            $this->uploadedFile = new UploadedFile(
                $movedFile->getPathname(),
                $movedFile->getFilename(),
                $movedFile->getMimeType(),
                null,
                true
            );

            return true;
        } catch (FileException) {
            return false;
        }
    }

    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    public function isAllowedExtension(string $extension): bool
    {
        return in_array(strtolower($extension), $this->allowedExtensions, true);
    }

    public function isExpectedExtension(string $extension): bool
    {
        return $this->expectedUploadedFileExtension === $extension;
    }

    public function getFileName(): ?string
    {
        return $this->uploadedFile ? $this->uploadedFile->getFilename() : null;
    }
}
