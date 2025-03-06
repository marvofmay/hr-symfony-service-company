<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\DeleteMultipleEmployeesCommand;
use App\Module\Company\Domain\DTO\Employee\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleEmployeesAction
{
    public function __construct(private MessageBusInterface $commandBus, private EmployeeReaderInterface $employeeReaderRepository)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultipleEmployeesCommand(
                $this->employeeReaderRepository->getEmployeesByUUID($deleteMultipleDTO->getSelectedUUID())
            )
        );
    }
}
