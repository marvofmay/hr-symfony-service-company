<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\ImportLog;

use Doctrine\Common\Collections\Collection;

interface ImportLogWriterInterface
{
    public function saveImportLogsInDB(Collection $importLogs): void;
}