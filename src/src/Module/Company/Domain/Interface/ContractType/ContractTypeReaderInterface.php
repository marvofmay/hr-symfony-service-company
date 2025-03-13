<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

interface ContractTypeReaderInterface
{
    public function getContractTypeByUUID(string $uuid): ?ContractType;

    public function getContractTypeByName(string $name, ?string $uuid): ?ContractType;
}
