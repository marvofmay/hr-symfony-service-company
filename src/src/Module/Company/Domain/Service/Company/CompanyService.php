<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

readonly class CompanyService
{
    public function __construct(private CompanyWriterInterface $companyWriterRepository)
    {
    }

    public function __toString()
    {
        return 'CompanyService';
    }

    public function saveCompanyInDB(Company $company): void
    {
        $this->companyWriterRepository->saveCompanyInDB($company);
    }

    public function updateCompanyInDB(Company $company): void
    {
        $this->companyWriterRepository->updateCompanyInDB($company);
    }

    public function saveCompaniesInDB(array $companies): void
    {
        $this->companyWriterRepository->saveCompaniesInDB($companies);
    }

    public function deleteMultipleCompaniesInDB(array $selectedUUID): void
    {
        $this->companyWriterRepository->deleteMultipleCompaniesInDB($selectedUUID);
    }
}
