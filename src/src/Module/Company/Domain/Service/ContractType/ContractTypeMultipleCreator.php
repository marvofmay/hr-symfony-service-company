<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use App\Module\Company\Domain\Service\ContractType\Import\ImportContractTypesFromXLSX;
use Doctrine\Common\Collections\ArrayCollection;

readonly class ContractTypeMultipleCreator
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function multipleCreate(array $data): void
    {
        $contractTypes = new ArrayCollection();
        foreach ($data as $item) {
            $contractType = new ContractType();
            $contractType->setName($item[ImportContractTypesFromXLSX::COLUMN_NAME]);
            $contractType->setDescription($item[ImportContractTypesFromXLSX::COLUMN_DESCRIPTION]);
            $contractType->setActive((bool) $item[ImportContractTypesFromXLSX::COLUMN_ACTIVE]);

            $contractTypes[] = $contractType;
        }

        $this->contractTypeWriterRepository->saveContractTypesInDB($contractTypes);
    }
}
