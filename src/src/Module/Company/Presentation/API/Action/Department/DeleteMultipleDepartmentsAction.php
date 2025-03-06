<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\DeleteMultipleDepartmentsCommand;
use App\Module\Company\Domain\DTO\Department\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleDepartmentsAction
{
    public function __construct(private MessageBusInterface $commandBus, private DepartmentReaderInterface $roleReaderRepository)
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->commandBus->dispatch(
            new DeleteMultipleDepartmentsCommand(
                $this->roleReaderRepository->getDepartmentsByUUID($deleteMultipleDTO->getSelectedUUID())
            )
        );
    }
}
