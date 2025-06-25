<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Module\Company\Domain\Entity\Industry;

final class IndustryUpdatedEvent extends IndustryEvent
{
    public function __construct(public readonly Industry $industry) {}

    public function getData(): array
    {
        return [
            Industry::COLUMN_NAME        => $this->industry->getName(),
            Industry::COLUMN_DESCRIPTION => $this->industry->getDescription(),
        ];
    }
}