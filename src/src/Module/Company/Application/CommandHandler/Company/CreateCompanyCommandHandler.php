<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\Service\Company\CompanyCreator;

readonly class CreateCompanyCommandHandler
{
    public function __construct(private CompanyCreator $companyCreator,)
    {
    }

    public function __invoke(CreateCompanyCommand $command): void
    {
        $this->companyCreator->create($command);
    }
}
