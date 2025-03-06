<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\UpdateCompanyCommand;
use App\Module\Company\Domain\Service\Company\CompanyUpdater;

readonly class UpdateCompanyCommandHandler
{
    public function __construct(private CompanyUpdater $companyUpdater,)
    {
    }

    public function __invoke(UpdateCompanyCommand $command): void
    {
        $this->companyUpdater->update($command->company, $command);
    }
}
