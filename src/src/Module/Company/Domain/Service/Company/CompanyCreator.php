<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

readonly class CompanyCreator
{
    public function __construct(private CompanyWriterInterface $companyWriterRepository, private CompanyReaderInterface $companyReaderRepository)
    {
    }

    public function create(CreateCompanyCommand $command): void
    {
        $company = new Company();

        $company->setFullName($command->fullName);
        $company->setShortName($command->shortName);
        $company->setActive($command->active);

        if (null !== $command->parentCompanyUUID) {
            $parentCompany = $this->companyReaderRepository->getCompanyByUUID($command->parentCompanyUUID);
            if ($parentCompany instanceof Company) {
                $company->setParentCompany($parentCompany);
            }
        }

        $this->companyWriterRepository->saveCompanyInDB($company);
    }
}