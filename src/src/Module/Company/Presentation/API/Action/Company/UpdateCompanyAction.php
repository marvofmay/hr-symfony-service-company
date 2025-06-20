<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\UpdateCompanyCommand;
use App\Module\Company\Domain\DTO\Company\UpdateDTO;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateCompanyAction
{
    public function __construct(private MessageBusInterface $commandBus, private CompanyReaderInterface $companyReaderRepository)
    {
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($updateDTO->getUUID());
        $this->commandBus->dispatch(
            new UpdateCompanyCommand(
                $company,
                $updateDTO->getFullName(),
                $updateDTO->getShortName(),
                $updateDTO->getActive(),
                $updateDTO->getParentCompanyUUID(),
                $updateDTO->getNip(),
                $updateDTO->getREGON(),
                $updateDTO->getDescription(),
                $updateDTO->getIndustryUUID(),
                $updateDTO->getPhones(),
                $updateDTO->getEmails(),
                $updateDTO->getWebsites(),
                $updateDTO->getAddress()
            )
        );
    }
}
