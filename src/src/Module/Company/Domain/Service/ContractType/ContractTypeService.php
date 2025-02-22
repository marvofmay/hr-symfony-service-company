<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class ContractTypeService
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function __toString()
    {
        return 'ContractTypeService';
    }

    public function saveContractTypeInDB(ContractType $contractType): void
    {
        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }

    public function updateContractTypeInDB(ContractType $contractType): void
    {
        $this->contractTypeWriterRepository->updateContractTypeInDB($contractType);
    }

    public function saveContractTypesInDB(array $contractTypes): void
    {
        $this->contractTypeWriterRepository->saveContractTypesInDB($contractTypes);
    }

    public function deleteMultipleContractTypesInDB(array $selectedUUID): void
    {
        $this->contractTypeWriterRepository->deleteMultipleContractTypesInDB($selectedUUID);
    }
}
