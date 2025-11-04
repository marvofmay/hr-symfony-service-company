<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

use App\Common\Domain\Interface\CommandInterface;

class CreateContractTypeCommand implements CommandInterface
{
    public const string CONTRACT_TYPE_NAME = 'name';
    public const string CONTRACT_TYPE_DESCRIPTION = 'description';
    public const string CONTRACT_TYPE_ACTIVE = 'active';

    public function __construct(public string $name, public ?string $description, public bool $active)
    {
    }
}
