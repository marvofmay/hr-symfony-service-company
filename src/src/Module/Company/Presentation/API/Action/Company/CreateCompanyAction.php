<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Application\Validator\Company\CompanyValidator;
use App\Module\Company\Application\Validator\Industry\IndustryValidator;
use App\Module\Company\Domain\DTO\Company\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateCompanyAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CompanyValidator    $companyValidator,
        private IndustryValidator   $industryValidator,
    )
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->companyValidator->isCompanyWithFullNameAlreadyExists($createDTO->fullName);
        if (null !== $createDTO->internalCode) {
            $this->companyValidator->isCompanyWithInternalCodeAlreadyExists($createDTO->internalCode);
        }
        $this->companyValidator->isCompanyAlreadyExists($createDTO->nip, $createDTO->regon);
        $this->industryValidator->isIndustryExists($createDTO->industryUUID);
        if (null !== $createDTO->parentCompanyUUID) {
            $this->companyValidator->isCompanyExists($createDTO->parentCompanyUUID);
        }

        $this->commandBus->dispatch(
            new CreateCompanyCommand(
                $createDTO->fullName,
                $createDTO->shortName,
                $createDTO->internalCode,
                $createDTO->active,
                $createDTO->parentCompanyUUID,
                $createDTO->nip,
                $createDTO->regon,
                $createDTO->description,
                $createDTO->industryUUID,
                $createDTO->phones,
                $createDTO->emails,
                $createDTO->websites,
                $createDTO->address
            )
        );
    }
}
