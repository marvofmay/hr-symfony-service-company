<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Module\Company\Domain\Entity\Company;

class UpdateCompanyCommand
{
    public function __construct(
        public Company $company,
        public string $fullName,
        public ?string $shortName,
        public bool $active,
        public ?Company $parentCompany
    ) {}
}
