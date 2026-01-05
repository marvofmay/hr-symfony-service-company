<?php

namespace App\Module\Company\Application\Command\ContractType;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteContractTypeCommand implements CommandInterface
{
    public const string CONTRACT_TYPE_UUID = 'contractTypeUUID';

    public function __construct(public string $contractTypeUUID)
    {
    }
}
