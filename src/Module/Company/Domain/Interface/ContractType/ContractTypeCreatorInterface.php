<?php

namespace App\Module\Company\Domain\Interface\ContractType;

interface ContractTypeCreatorInterface
{
    public function create(string $name, ?string $description, bool $active = false): void;
}
