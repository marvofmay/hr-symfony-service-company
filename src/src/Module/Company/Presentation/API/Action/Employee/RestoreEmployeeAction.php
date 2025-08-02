<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\Company\Application\Command\Employee\RestoreEmployeeCommand;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class RestoreEmployeeAction
{
    public function __construct(
        private MessageBusInterface    $commandBus,
        private EmployeeReaderInterface $employeeReaderRepository,
    ) {
    }

    public function execute(string $uuid): void
    {
        $company = $this->employeeReaderRepository->getDeletedEmployeeByUUID($uuid);
        $this->commandBus->dispatch(new RestoreEmployeeCommand($company));
    }
}
