<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class UpdatePositionCommandHandler
{
    public function __construct(private PositionWriterInterface $positionWriterRepository, private DepartmentReaderInterface $departmentReaderRepository,)
    {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $position = $command->getPosition();
        $position->setName($command->getName());
        $position->setDescription($command->getDescription());
        $position->setActive($command->getActive());

        $departments = $position->getDepartments();
        foreach ($departments as $department) {
            $position->removeDepartment($department);
        }

        $departments = new ArrayCollection();
        foreach ($command->getDepartmentsUUID() as $departmentUUID) {
            $departments[] = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
        }

        $position->setUpdatedAt(new \DateTime());

        $this->positionWriterRepository->updatePositionInDB($position, $departments);
    }
}
