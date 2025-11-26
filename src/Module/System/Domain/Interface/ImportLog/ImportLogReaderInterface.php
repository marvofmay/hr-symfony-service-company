<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\ImportLog;

use App\Module\System\Domain\Entity\Import;
use Doctrine\Common\Collections\Collection;

interface ImportLogReaderInterface
{
    public function getImportLogsByImport(Import $import): Collection;
}
