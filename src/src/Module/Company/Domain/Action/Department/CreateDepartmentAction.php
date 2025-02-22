<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Action\Department;

use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\DTO\Department\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateDepartmentAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateDepartmentCommand(
                $createDTO->getName(),
                $createDTO->getActive(),
                $createDTO->getCompanyUUID(),
                $createDTO->getParentDepartmentUUID(),
            )
        );
    }
}
