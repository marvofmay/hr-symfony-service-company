<?php

declare(strict_types=1);

namespace App\Module\System\Application\QueryHandler\File;

use App\Module\System\Application\Query\File\GetFileByNamePathAndKindQuery;
use App\Module\System\Domain\Interface\File\FileReaderInterface;
use Ramsey\Uuid\UuidInterface;

class GetFileByNamePathAndKindQueryHandler
{
    public function __construct(private FileReaderInterface $fileReaderRepository,)
    {
    }

    public function __invoke(GetFileByNamePathAndKindQuery $query): ?UuidInterface
    {
        $file = $this->fileReaderRepository->getFileByNamePathAndKind($query->fileName, $query->filePath, $query->fileKind);

        return $file?->getUUID();
    }
}