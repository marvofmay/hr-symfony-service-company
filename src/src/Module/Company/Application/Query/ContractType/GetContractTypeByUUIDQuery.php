<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\ContractType;

final readonly class GetContractTypeByUUIDQuery
{
    public function __construct(public string $uuid)
    {
    }
}
