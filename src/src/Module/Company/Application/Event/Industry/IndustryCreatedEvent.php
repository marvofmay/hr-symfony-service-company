<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Industry;

final readonly class IndustryCreatedEvent implements DomainEventInterface
{
    public function __construct(public Industry $industry)
    {
    }

    public function getData(): array
    {
        return [
            Industry::COLUMN_NAME        => $this->industry->getName(),
            Industry::COLUMN_DESCRIPTION => $this->industry->getDescription(),
        ];
    }
}