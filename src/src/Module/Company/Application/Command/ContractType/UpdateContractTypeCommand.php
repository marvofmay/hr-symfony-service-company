<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

class UpdateContractTypeCommand
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $name,
        private readonly ?string $description,
        private readonly ?bool $active,
        private readonly ContractType $contractType,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getContractType(): ContractType
    {
        return $this->contractType;
    }
}
