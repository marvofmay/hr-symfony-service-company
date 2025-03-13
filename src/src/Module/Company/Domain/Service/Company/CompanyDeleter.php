<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

readonly class CompanyDeleter
{
    public function __construct(private CompanyWriterInterface $companyWriterRepository)
    {
    }

    public function delete(Company $company): void
    {
        $this->companyWriterRepository->deleteCompanyInDB($company);
    }
}