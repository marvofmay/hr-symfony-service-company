<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use Doctrine\Common\Collections\Collection;

class DeleteMultiplePositionsCommand
{
    public function __construct(private readonly Collection $positions)
    {
    }

    public function getPositions(): Collection
    {
        return $this->positions;
    }
}
