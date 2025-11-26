<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

use App\Common\Domain\Interface\CommandInterface;

final readonly class RestoreContractTypeCommand implements CommandInterface
{
    public const string CONTRACT_TYPE_UUID = 'contractTypeUUID';

    public function __construct(public string $contractTypeUUID)
    {
    }
}
