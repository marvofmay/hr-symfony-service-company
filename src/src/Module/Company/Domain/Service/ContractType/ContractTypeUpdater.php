<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class ContractTypeUpdater
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function update(ContractType $contractType, string $name, ?string $description, ?bool $active): void
    {
        $contractType->setName($name);
        $contractType->setDescription($description);
        $contractType->setActive($active);
        //$contractType->setUpdatedAt(new \DateTime());

        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
