<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class CompanyMultipleCreator
{
    public function __construct(private CompanyWriterInterface $companyWriterRepository, private CompanyReaderInterface $companyReaderRepository,)
    {
    }

    public function multipleCreate(array $data): void
    {
        $companies = new ArrayCollection();
        foreach ($data as $item) {
            $company = new Company();
            $company->setFullName($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_FULL_NAME]);
            $company->setShortName($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_SHORT_NAME]);
            if (null !== $item[2]) {
                $parentCompany = $this->companyReaderRepository->getCompanyByUUID($item[ImportCompaniesFromXLSX::COLUMN_PARENT_COMPANY_UUID]);
                if ($parentCompany instanceof Company) {
                    $company->setParentCompany($parentCompany);
                }
            }
            $company->setNip((string)$item[ImportCompaniesFromXLSX::COLUMN_NIP]);
            $company->setRegon((string)$item[ImportCompaniesFromXLSX::COLUMN_REGON]);
            $company->setActive((bool)$item[ImportCompaniesFromXLSX::COLUMN_ACTIVE]);

            $companies[] = $company;
        }

        $this->companyWriterRepository->saveCompaniesInDB($companies);
    }
}