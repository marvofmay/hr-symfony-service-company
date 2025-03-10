<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Import;

use App\Module\System\Domain\Entity\Import;

interface ImportWriterInterface
{
    public function saveImportInDB(Import $import): void;
}