<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

class CreateContractTypeCommand
{
    public function __construct(public string $name, public ?string $description, public bool $active)
    {
    }
}
