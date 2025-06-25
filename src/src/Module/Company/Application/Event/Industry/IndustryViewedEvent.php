<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Module\Company\Domain\Entity\Industry;

final class IndustryViewedEvent extends IndustryEvent
{
    public function __construct(public readonly string $uuid)
    {
    }

    public function getData(): array
    {
        return [
            Industry::COLUMN_UUID => $this->uuid,
        ];
    }
}