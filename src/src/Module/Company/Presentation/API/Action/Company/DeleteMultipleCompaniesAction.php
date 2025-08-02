<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\DeleteMultipleCompaniesCommand;
use App\Module\Company\Application\Validator\Company\CompanyValidator;
use App\Module\Company\Domain\DTO\Company\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleCompaniesAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CompanyReaderInterface $companyReaderRepository,
        private CompanyValidator $companyValidator,
    )
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->companyValidator->isCompaniesExists($deleteMultipleDTO->getSelectedUUID());
        $this->commandBus->dispatch(
            new DeleteMultipleCompaniesCommand(
                $this->companyReaderRepository->getCompaniesByUUID($deleteMultipleDTO->getSelectedUUID())
            )
        );
    }
}
