<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Company;

use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\DTO\Company\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateCompanyAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateCompanyCommand(
                $createDTO->getFullName(),
                $createDTO->getShortName(),
                $createDTO->getActive(),
                $createDTO->getParentCompanyUUID(),
                $createDTO->getNip(),
                $createDTO->getREGON(),
                $createDTO->getDescription(),
                $createDTO->getIndustryUUID(),
                $createDTO->getPhones(),
                $createDTO->getEmails(),
                $createDTO->getWebsites(),
                $createDTO->getAddress()
            )
        );
    }
}
