<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Service\Position\PositionService;
use Doctrine\Common\Collections\ArrayCollection;

readonly class CreatePositionCommandHandler
{
    public function __construct(private PositionService $positionService, private DepartmentReaderInterface $departmentReaderRepository)
    {
    }

    public function __invoke(CreatePositionCommand $command): void
    {
        $departments = new ArrayCollection();

        $position = new Position();
        $position->setName($command->name);
        $position->setDescription($command->description);
        $position->setActive($command->active);

        foreach ($command->departmentsUUID as $departmentUUID) {
            $departments[] = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
        }

        $this->positionService->savePositionInDB($position, $departments);
    }
}
