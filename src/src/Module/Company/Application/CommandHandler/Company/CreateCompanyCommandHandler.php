<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Service\Company\CompanyService;

readonly class CreateCompanyCommandHandler
{
    public function __construct(private CompanyService $companyService, private CompanyReaderInterface $companyReaderRepository)
    {
    }

    public function __invoke(CreateCompanyCommand $command): void
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

        $this->companyService->saveCompanyInDB($company);
    }
}
