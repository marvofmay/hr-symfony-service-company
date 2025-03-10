<?php

declare(strict_types=1);

namespace App\Module\System\Application\QueryHandler\File;

use App\Module\System\Application\Query\File\GetFileByNamePathAndKindQuery;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Interface\File\FileReaderInterface;

class GetFileByNamePathAndKindQueryHandler
{
    public function __construct(private FileReaderInterface $fileReaderRepository,)
    {
    }

    public function __invoke(GetFileByNamePathAndKindQuery $query): ?File
    {
        return $this->fileReaderRepository->getFileByNamePathAndKind($query->fileName, $query->filePath, $query->fileKind);
    }
}