<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\RestoreDepartmentCommand;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class RestoreDepartmentAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private DepartmentReaderInterface $departmentReaderRepository,
    ) {
    }

    public function execute(string $uuid): void
    {
        $department = $this->departmentReaderRepository->getDeletedDepartmentByUUID($uuid);
        $this->commandBus->dispatch(new RestoreDepartmentCommand($department));
    }
}
