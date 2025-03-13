<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\File;

use App\Common\Domain\Enum\FileKindEnum;
use App\Module\System\Domain\Entity\File;

interface FileReaderInterface
{
    public function getFileByNamePathAndKind(string $fileName, string $filePath, FileKindEnum $enum): ?File;
}