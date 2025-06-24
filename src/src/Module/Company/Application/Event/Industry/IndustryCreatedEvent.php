<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;

final readonly class IndustryCreatedEvent implements LoggableEventInterface
{
    public function __construct(public Industry $industry)
    {
    }

    public function getEntityClass(): string
    {
        return Industry::class;
    }

    public function getData(): array
    {
        return [
            Industry::COLUMN_NAME        => $this->industry->getName(),
            Industry::COLUMN_DESCRIPTION => $this->industry->getDescription(),
        ];
    }
}