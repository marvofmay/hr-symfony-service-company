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
            $company->setFullName($item[0]);
            $company->setShortName($item[1]);
            if (null !== $item[2]) {
                $parentCompany = $this->companyReaderRepository->getCompanyByUUID($item[2]);
                if ($parentCompany instanceof Company) {
                    $company->setParentCompany($parentCompany);
                }
            }
            $company->setActive((bool)$item[3]);

            $companies[] = $company;
        }

        $this->companyWriterRepository->saveCompaniesInDB($companies);
    }
}