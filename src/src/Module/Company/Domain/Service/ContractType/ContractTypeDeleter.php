<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class ContractTypeDeleter
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function delete(ContractType $contractType): void
    {
        $this->contractTypeWriterRepository->deleteContractTypeInDB($contractType);
    }
}