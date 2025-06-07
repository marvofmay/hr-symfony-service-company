<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
use App\Module\Company\Domain\DTO\Employee\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateEmployeeAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateEmployeeCommand(
                $createDTO->getDepartmentUUID(),
                $createDTO->getPositionUUID(),
                $createDTO->getContractTypeUUID(),
                $createDTO->getRoleUUID(),
                $createDTO->getParentEmployeeUUID(),
                $createDTO->getExternalUUID(),
                $createDTO->getEmail(),
                $createDTO->getFirstName(),
                $createDTO->getLastName(),
                $createDTO->getPESEL(),
                $createDTO->getEmploymentFrom(),
                $createDTO->getEmploymentTo(),
                $createDTO->getActive(),
                $createDTO->getPhones(),
                $createDTO->getAddress()
            )
        );
    }
}
