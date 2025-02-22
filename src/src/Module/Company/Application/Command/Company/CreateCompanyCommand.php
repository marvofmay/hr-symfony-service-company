<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

class CreateCompanyCommand
{
    public function __construct(public string $fullName, public ?string $shortName, public bool $active, public ?string $parentCompanyUUID)
    {
    }
}
