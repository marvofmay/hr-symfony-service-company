<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeRestorerInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

final readonly class ContractTypeRestorer implements ContractTypeRestorerInterface
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function restore(ContractType $contractType): void
    {
        $contractType->setDeletedAt(null);
        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
