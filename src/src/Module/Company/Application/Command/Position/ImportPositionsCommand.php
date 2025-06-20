<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

class ImportPositionsCommand
{
    public function __construct(private string $importUUID)
    {
    }

    public function getImportUUID(): string
    {
        return $this->importUUID;
    }
}
