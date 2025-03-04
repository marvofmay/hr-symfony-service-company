<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Application\Command\Company\UpdateCompanyCommand;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

readonly class CompanyUpdater
{
    public function __construct(private CompanyWriterInterface $companyWriterRepository, private CompanyReaderInterface $companyReaderRepository,)
    {
    }

    public function update(UpdateCompanyCommand $command): void
    {
        $company = $command->company;
        $company->setFullName($command->fullName);
        $company->setShortName($command->shortName);
        $company->setActive($command->active);

        if (null !== $command->parentCompanyUUID) {
            if (null === $company->getParentCompany()) {
                $company->setParentCompany($this->companyReaderRepository->getCompanyByUUID($command->parentCompanyUUID));
            } else {
                if ($company->getParentCompany()->getUUID()->toString() !== $command->parentCompanyUUID) {
                    $company->removeParentCompany();
                    $company->setParentCompany($this->companyReaderRepository->getCompanyByUUID($command->parentCompanyUUID));
                }
            }
        }

        $this->companyWriterRepository->updateCompanyInDB($company);
    }
}