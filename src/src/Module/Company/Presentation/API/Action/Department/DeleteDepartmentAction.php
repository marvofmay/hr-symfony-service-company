<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\DeleteDepartmentCommand;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteDepartmentAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
    ) {}

    public function execute(string $uuid): void
    {
        $this->commandBus->dispatch(new DeleteDepartmentCommand($this->departmentReaderRepository->getDepartmentByUUID($uuid)));
    }
}
