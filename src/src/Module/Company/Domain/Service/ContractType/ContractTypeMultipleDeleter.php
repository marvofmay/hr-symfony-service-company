<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class ContractTypeMultipleDeleter
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function multipleDelete(Collection $contractTypes): void
    {
        $this->contractTypeWriterRepository->deleteMultipleContractTypesInDB($contractTypes);
    }
}