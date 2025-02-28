<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class CreatePositionCommandHandler
{
    public function __construct(
        private Position $position,
        private DepartmentReaderInterface $departmentReaderRepository,
        private PositionWriterInterface $positionWriterRepository,
    ) {}

    public function __invoke(CreatePositionCommand $command): void
    {
        $departments = new ArrayCollection();

        $this->position->setName($command->name);
        $this->position->setDescription($command->description);
        $this->position->setActive($command->active);

        foreach ($command->departmentsUUID as $departmentUUID) {
            $departments[] = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
        }

        $this->positionWriterRepository->savePositionInDB($this->position, $departments);
    }
}
