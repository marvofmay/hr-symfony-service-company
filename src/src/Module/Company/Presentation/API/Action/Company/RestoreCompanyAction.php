<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\RestoreCompanyCommand;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class RestoreCompanyAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    public function execute(string $uuid): void
    {
        $company = $this->companyReaderRepository->getDeletedCompanyByUUID($uuid);
        $this->commandBus->dispatch(new RestoreCompanyCommand($company));
    }
}
