<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\DeleteMultipleEmployeesCommand;
use App\Module\Company\Application\Validator\Employee\EmployeeValidator;
use App\Module\Company\Domain\DTO\Employee\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleEmployeesAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private EmployeeReaderInterface $employeeReaderRepository,
        private EmployeeValidator $employeeValidator,
    )
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->employeeValidator->isEmployeesExists($deleteMultipleDTO->selectedUUID);
        $this->commandBus->dispatch(
            new DeleteMultipleEmployeesCommand(
                $this->employeeReaderRepository->getEmployeesByUUID($deleteMultipleDTO->selectedUUID)
            )
        );
    }
}
