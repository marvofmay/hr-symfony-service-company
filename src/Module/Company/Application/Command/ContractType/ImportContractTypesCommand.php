<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

class ImportContractTypesCommand
{
    public const string IMPORT_UUID = 'importUUID';

    public function __construct(public string $importUUID)
    {
    }
}
