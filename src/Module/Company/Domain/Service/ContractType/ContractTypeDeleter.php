<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeDeleterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

final readonly class ContractTypeDeleter implements ContractTypeDeleterInterface
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function delete(ContractType $contractType): void
    {
        $this->contractTypeWriterRepository->deleteContractTypeInDB($contractType);
    }
}
