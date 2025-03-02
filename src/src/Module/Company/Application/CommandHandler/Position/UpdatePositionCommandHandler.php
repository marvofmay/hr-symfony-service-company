<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\UpdatePositionCommand;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use App\Module\Company\Domain\Service\Position\PositionUpdater;
use Doctrine\Common\Collections\ArrayCollection;

readonly class UpdatePositionCommandHandler
{
    public function __construct(private PositionUpdater $positionUpdater,)
    {
    }

    public function __invoke(UpdatePositionCommand $command): void
    {
        $this->positionUpdater->update($command);
    }
}
