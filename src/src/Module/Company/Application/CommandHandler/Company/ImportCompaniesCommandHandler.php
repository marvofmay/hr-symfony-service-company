<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\ImportCompaniesCommand;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Service\Company\CompanyService;

readonly class ImportCompaniesCommandHandler
{
    public function __construct(private CompanyService $companyService)
    {
    }

    public function __invoke(ImportCompaniesCommand $command): void
    {
        $companies = [];
        foreach ($command->data as $item) {
            $company = new Company();
            $company->setFullName($item[0]);
            $company->setShortName($item[1]);
            $company->setActive((bool)$item[3]);

            $companies[] = $company;
        }

        $this->companyService->saveCompaniesInDB($companies);
    }
}
