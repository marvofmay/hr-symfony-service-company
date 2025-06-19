<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Application\Validator\Position\PositionValidator;
use App\Module\Company\Domain\DTO\Position\UpdateDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdatePositionAction
{
    public function __construct(
        private MessageBusInterface     $commandBus,
        private PositionReaderInterface $positionReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private PositionValidator $positionValidator,
    )
    {
    }

    public function execute(string $uuid, UpdateDTO $updateDTO): void
    {
        try {
            $position = $this->positionReaderRepository->getPositionByUUID($uuid);
            $this->positionValidator->isPositionNameAlreadyExists($updateDTO->name, $uuid);
            $departments = $this->departmentReaderRepository->getDepartmentsByUUID($updateDTO->departmentsUUID);
            $this->positionValidator->isPositionAlreadyAssignedToDepartments($position, $departments);

            $this->commandBus->dispatch(
                new UpdatePositionCommand(
                    $updateDTO->name,
                    $updateDTO->description,
                    $updateDTO->active,
                    $updateDTO->departmentsUUID,
                    $position
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
