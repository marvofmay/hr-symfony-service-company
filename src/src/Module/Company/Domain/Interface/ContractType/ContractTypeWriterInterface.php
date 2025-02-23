<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

interface ContractTypeWriterInterface
{
    public function saveContractTypeInDB(ContractType $contractType): void;

    public function updateContractTypeInDB(ContractType $contractType): void;

    public function saveContractTypesInDB(array $contractTypes): void;
    public function deleteMultipleContractTypesInDB(array $selectedUUID): void;
}
