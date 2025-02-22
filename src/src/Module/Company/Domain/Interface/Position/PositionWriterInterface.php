<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;

interface PositionWriterInterface
{
    public function savePositionInDB(Position $position): void;

    public function updatePositionInDB(Position $position): void;

    public function savePositionsInDB(array $positions): void;
    public function deleteMultiplePositionsInDB(array $selectedUUID): void;
}
