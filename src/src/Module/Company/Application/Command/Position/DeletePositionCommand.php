<?php

namespace App\Module\Company\Application\Command\Position;

use App\Module\Company\Domain\Entity\Position;

readonly class DeletePositionCommand
{
    public function __construct(private Position $position)
    {
    }

    public function getPosition(): Position
    {
        return $this->position;
    }
}
