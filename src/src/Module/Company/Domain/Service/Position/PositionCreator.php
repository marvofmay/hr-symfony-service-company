<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

readonly class PositionCreator
{
    public function __construct(private PositionWriterInterface $positionWriterRepository)
    {
    }

    public function create(CreatePositionCommand $command): void
    {
        $position = new Position();
        $position->setName($command->name);
        $position->setDescription($command->description);
        $position->setActive($command->active);

        foreach ($command->departments as $department) {
            $position->addDepartment($department);
        }

        $this->positionWriterRepository->savePositionInDB($position);
    }
}
