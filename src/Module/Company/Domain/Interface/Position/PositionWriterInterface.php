<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;
use Doctrine\Common\Collections\Collection;

interface PositionWriterInterface
{
    public function savePositionInDB(Position $position): void;

    public function savePositionsInDB(Collection $positions): void;

    public function deletePositionInDB(Position $position): void;

    public function deleteMultiplePositionsInDB(Collection $positions): void;

    public function restorePositionInDB(Position $position): void;
}
