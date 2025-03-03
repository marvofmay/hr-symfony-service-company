<?php

namespace App\Module\Company\Application\Command\Company;

use App\Module\Company\Domain\Entity\Company;

readonly class DeleteCompanyCommand
{
    public function __construct(private Company $company)
    {
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
