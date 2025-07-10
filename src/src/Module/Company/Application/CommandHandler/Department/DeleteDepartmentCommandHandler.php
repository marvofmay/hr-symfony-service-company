<?php

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Module\Company\Application\Command\Department\DeleteDepartmentCommand;
use App\Module\Company\Domain\Service\Department\DepartmentDeleter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteDepartmentCommandHandler
{
    public function __construct(private DepartmentDeleter $departmentDeleter)
    {
    }

    public function __invoke(DeleteDepartmentCommand $command): void
    {
        $this->departmentDeleter->delete($command->getDepartment());
    }
}
