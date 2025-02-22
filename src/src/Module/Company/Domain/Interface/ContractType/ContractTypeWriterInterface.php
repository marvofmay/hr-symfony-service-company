<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

interface ContractTypeWriterInterface
{
    public function saveContractTypeInDB(ContractType $position): void;

    public function updateContractTypeInDB(ContractType $position): void;

    public function saveContractTypesInDB(array $positions): void;
}
