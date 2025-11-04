<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

use App\Common\Domain\Interface\CommandInterface;

class DeleteMultipleContractTypesCommand implements CommandInterface
{
    public const string CONTRACT_TYPES_UUIDS = 'contactTypesUUIDs';

    public function __construct(public array $contractTypesUUIDs)
    {
    }
}
