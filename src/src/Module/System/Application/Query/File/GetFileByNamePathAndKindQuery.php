<?php

declare(strict_types=1);

namespace App\Module\System\Application\Query\File;

use App\Common\Domain\Enum\FileKindEnum;

readonly class GetFileByNamePathAndKindQuery
{
    public function __construct(
        public string $fileName,
        public string $filePath,
        public FileKindEnum $fileKind,
    ) {}
}