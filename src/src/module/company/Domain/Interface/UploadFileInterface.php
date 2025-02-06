<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Interface;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadFileInterface
{
    public function uploadFile(UploadedFile $file): bool;
    public function getUploadedFile(): ?UploadedFile;
    public function isAllowedExtension(string $extension): bool;
    public function isExpectedExtension(string $extension): bool;
    public function getFileName(): ?string;
}