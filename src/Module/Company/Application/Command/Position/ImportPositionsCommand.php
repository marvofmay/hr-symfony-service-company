<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

class ImportPositionsCommand
{
    public const string IMPORT_UUID = 'importUUID';

    public function __construct(public string $importUUID)
    {
    }
}
