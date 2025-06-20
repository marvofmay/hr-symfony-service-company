<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Department;

use App\Module\Company\Application\Command\Department\UpdateDepartmentCommand;
use App\Module\Company\Domain\DTO\Department\UpdateDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateDepartmentAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private DepartmentReaderInterface $departmentReaderRepository,
    ) {
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $department = $this->departmentReaderRepository->getDepartmentByUUID($updateDTO->getUUID());
        $this->commandBus->dispatch(
            new UpdateDepartmentCommand(
                $department,
                $updateDTO->getName(),
                $updateDTO->getDescription(),
                $updateDTO->getActive(),
                $updateDTO->getCompanyUUID(),
                $updateDTO->getParentDepartmentUUID(),
                $updateDTO->getPhones(),
                $updateDTO->getEmails(),
                $updateDTO->getWebsites(),
                $updateDTO->getAddress()
            )
        );
    }
}
