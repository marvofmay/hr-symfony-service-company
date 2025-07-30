<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\DeleteCompanyCommand;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteCompanyAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    public function execute(string $uuid): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($uuid);
        $this->commandBus->dispatch(new DeleteCompanyCommand($company));
    }
}
