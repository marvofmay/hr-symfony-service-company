<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Application\Validator\Position\PositionValidator;
use App\Module\Company\Domain\DTO\Position\CreateDTO;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreatePositionAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private PositionValidator $positionValidator,
        private DepartmentReaderInterface $departmentReaderRepository,
    ) {
    }

    public function execute(CreateDTO $createDTO): void
    {
        try {
            $this->positionValidator->isPositionNameAlreadyExists($createDTO->name);
            $departments = $this->departmentReaderRepository->getDepartmentsByUUID($createDTO->departmentsUUID);

            $this->commandBus->dispatch(
                new CreatePositionCommand(
                    $createDTO->name,
                    $createDTO->description,
                    $createDTO->active,
                    $departments,
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
