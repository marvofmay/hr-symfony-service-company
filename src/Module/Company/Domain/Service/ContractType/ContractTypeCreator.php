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

    public function create(string $name, ?string $description, bool $active = false): void
    {
        $contractType = ContractType::create(trim($name), $description, $active);

        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
