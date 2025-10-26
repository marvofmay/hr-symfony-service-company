<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event;

use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;

abstract class Event implements LoggableEventInterface
{
    public function __construct(public readonly array $data)
    {
    }

    abstract public function getEntityClass(): string;

    public function getData(): array
    {
        return $this->data;
    }
}
