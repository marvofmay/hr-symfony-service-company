<?php

declare(strict_types=1);

namespace App\Module\System\Application\QueryHandler\Import;

use App\Module\System\Application\Query\Import\GetImportByFileQuery;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;

class GetImportByFileQueryHandler
{
    public function __construct(private ImportReaderInterface $importReaderRepository,)
    {
    }

    public function __invoke(GetImportByFileQuery $query): ?Import
    {
        return $this->importReaderRepository->getImportByFile($query->file);
    }
}