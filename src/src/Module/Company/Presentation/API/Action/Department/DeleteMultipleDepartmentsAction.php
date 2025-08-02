<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\DeleteMultipleDepartmentsCommand;
use App\Module\Company\Application\Validator\Department\DepartmentValidator;
use App\Module\Company\Domain\DTO\Department\DeleteMultipleDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteMultipleDepartmentsAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private DepartmentReaderInterface $departmentReaderRepository,
        private DepartmentValidator $departmentValidator,
    )
    {
    }

    public function execute(DeleteMultipleDTO $deleteMultipleDTO): void
    {
        $this->departmentValidator->isDepartmentsExists($deleteMultipleDTO->selectedUUID);
        $this->commandBus->dispatch(
            new DeleteMultipleDepartmentsCommand(
                $this->departmentReaderRepository->getDepartmentsByUUID($deleteMultipleDTO->selectedUUID)
            )
        );
    }
}
