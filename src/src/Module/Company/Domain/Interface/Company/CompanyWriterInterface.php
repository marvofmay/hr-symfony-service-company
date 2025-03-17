<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Company;

use App\Module\Company\Domain\Entity\Company;
use Doctrine\Common\Collections\Collection;

interface CompanyWriterInterface
{
    public function saveOrUpdateCompanyInDB(Company $company): void;
    public function saveCompaniesInDB(Collection $companies): void;
    public function deleteCompanyInDB(Company $company): void;
    public function deleteMultipleCompaniesInDB(Collection $companies): void;
}
