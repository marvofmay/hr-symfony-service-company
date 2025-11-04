<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\ContractType;

use App\Common\Domain\Interface\QueryInterface;

final readonly class GetContractTypeByUUIDQuery implements QueryInterface
{
    public const string CONTRACT_TYPE_UUID = 'contractTypeUUID';
    public function __construct(public string $contractTypeUUID)
    {
    }
}
