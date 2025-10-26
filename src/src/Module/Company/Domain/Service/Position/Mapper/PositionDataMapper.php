<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Mapper;

use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Domain\Entity\Position;

final class PositionDataMapper
{
    public function map(Position $position, CommandInterface $command): void
    {
        $position->name = $command->name;
        $position->description = $command->description;
        $position->active = $command->active;
    }
}
