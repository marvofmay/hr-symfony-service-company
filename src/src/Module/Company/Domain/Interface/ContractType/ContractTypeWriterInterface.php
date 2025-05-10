<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use Doctrine\Common\Collections\Collection;

interface ContractTypeWriterInterface
{
    public function saveContractTypeInDB(ContractType $contractType): void;
    public function saveContractTypesInDB(Collection $contractTypes): void;
    public function deleteContractTypeInDB(ContractType $contractType): void;
    public function deleteMultipleContractTypesInDB(Collection $selectedUUID): void;
}
