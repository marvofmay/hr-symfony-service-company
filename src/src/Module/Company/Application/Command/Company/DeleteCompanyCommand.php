<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Module\Company\Domain\Entity\Company;

final readonly class DeleteCompanyCommand
{
    public function __construct(private Company $company)
    {
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
