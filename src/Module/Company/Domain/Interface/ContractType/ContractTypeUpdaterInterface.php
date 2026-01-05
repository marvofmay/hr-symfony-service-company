<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;

interface ContractTypeUpdaterInterface
{
    public function update(ContractType $contractType, string $name, ?string $description, bool $active = false): void;
}
