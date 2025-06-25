<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;

abstract class IndustryEvent implements LoggableEventInterface
{
    public function getEntityClass(): string
    {
        return Industry::class;
    }

    abstract public function getData(): array;
}