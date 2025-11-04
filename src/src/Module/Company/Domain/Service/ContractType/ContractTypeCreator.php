<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeCreatorInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

readonly class ContractTypeCreator implements ContractTypeCreatorInterface
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function create(string $name, ?string $description, ?bool $active = null): void
    {
        $contractType = new ContractType();
        $contractType->setName($name);
        $contractType->setDescription($description);
        if ($active !== null) {
            $contractType->setActive($active);
        }

        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
