<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

class ImportContractTypesCommand
{
    public function __construct(public array $data)
    {
    }
}
