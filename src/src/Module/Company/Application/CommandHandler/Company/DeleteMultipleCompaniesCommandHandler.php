<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\DeleteMultipleCompaniesCommand;
use App\Module\Company\Domain\Service\Company\CompanyMultipleDeleter;

readonly class DeleteMultipleCompaniesCommandHandler
{
    public function __construct(private CompanyMultipleDeleter $companyMultipleDeleter)
    {
    }

    public function __invoke(DeleteMultipleCompaniesCommand $command): void
    {
        $this->companyMultipleDeleter->multipleDelete($command->companies);
    }
}
