<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class PositionService
{
    public function __construct(private PositionWriterInterface $positionWriterRepository)
    {
    }

    public function __toString()
    {
        return 'PositionService';
    }

    public function savePositionInDB(Position $position, Collection $departments): void
    {
        $this->positionWriterRepository->savePositionInDB($position, $departments);
    }

    public function updatePositionInDB(Position $position): void
    {
        $this->positionWriterRepository->updatePositionInDB($position);
    }

    public function savePositionsInDB(array $positions): void
    {
        $this->positionWriterRepository->savePositionsInDB($positions);
    }

    public function deleteMultiplePositionsInDB(array $selectedUUID): void
    {
        $this->positionWriterRepository->deleteMultiplePositionsInDB($selectedUUID);
    }
}
