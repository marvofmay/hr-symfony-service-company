<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Import;

use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;

interface ImportReaderInterface
{
    public function getImportByFile(File $file): ?Import;

    public function getImportByUuid(string $uuid): ?Import;
}
