<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionDeleterInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;

readonly class PositionDeleter implements PositionDeleterInterface
{
    public function __construct(private PositionWriterInterface $positionWriterRepository)
    {
    }

    public function delete(Position $position): void
    {
        $this->positionWriterRepository->deletePositionInDB($position);
    }
}
