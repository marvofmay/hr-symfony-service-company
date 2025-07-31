<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\UpdateCompanyCommand;
use App\Module\Company\Application\Validator\Company\CompanyValidator;
use App\Module\Company\Application\Validator\Industry\IndustryValidator;
use App\Module\Company\Domain\DTO\Company\UpdateDTO;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateCompanyAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CompanyReaderInterface $companyReaderRepository,
        private CompanyValidator $companyValidator,
        private IndustryValidator $industryValidator,
    )
    {
    }

    public function execute(string $uuid, UpdateDTO $updateDTO): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($uuid);
        $this->companyValidator->isCompanyWithFullNameAlreadyExists($updateDTO->fullName, $uuid);
        $this->companyValidator->isCompanyAlreadyExists($updateDTO->nip, $updateDTO->regon, $uuid);
        $this->industryValidator->isIndustryExists($updateDTO->industryUUID);
        if (null !== $updateDTO->parentCompanyUUID) {
            $this->companyValidator->isCompanyExists($updateDTO->parentCompanyUUID);
        }

        $this->commandBus->dispatch(
            new UpdateCompanyCommand(
                $company,
                $updateDTO->fullName,
                $updateDTO->shortName,
                $updateDTO->active,
                $updateDTO->parentCompanyUUID,
                $updateDTO->nip,
                $updateDTO->regon,
                $updateDTO->description,
                $updateDTO->industryUUID,
                $updateDTO->phones,
                $updateDTO->emails,
                $updateDTO->websites,
                $updateDTO->address
            )
        );
    }
}
