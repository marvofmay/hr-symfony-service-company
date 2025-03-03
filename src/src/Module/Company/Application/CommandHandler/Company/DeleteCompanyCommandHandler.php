<?php

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\DeleteCompanyCommand;
use App\Module\Company\Domain\Service\Company\CompanyDeleter;

readonly class DeleteCompanyCommandHandler
{
    public function __construct(private CompanyDeleter $companyDeleter,)
    {
    }

    public function __invoke(DeleteCompanyCommand $command): void
    {
        $this->companyDeleter->delete($command->getCompany());
    }
}
