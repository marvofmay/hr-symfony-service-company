<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class PositionMultipleDeleter
{
    public function __construct(private PositionWriterInterface $positionWriterRepository)
    {
    }

    public function multipleDelete(Collection $positions): void
    {
        $this->positionWriterRepository->deleteMultiplePositionsInDB($positions);
    }
}