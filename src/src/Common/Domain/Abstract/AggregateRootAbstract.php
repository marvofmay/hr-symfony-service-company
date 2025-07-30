<?php

declare(strict_types=1);

namespace App\Common\Domain\Abstract;

use App\Common\Domain\Interface\AggregateRootInterface;
use App\Common\Domain\Interface\DomainEventInterface;

abstract class AggregateRootAbstract implements AggregateRootInterface
{
    protected array $recordedEvents = [];

    public function pullEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public function record(DomainEventInterface $event): void
    {
        $this->recordedEvents[] = $event;

        $this->apply($event);
    }

    public static function reconstituteFromHistory(array $events): static
    {
        $instance = new static();
        foreach ($events as $event) {
            $instance->apply($event);
        }

        return $instance;
    }

    abstract protected function apply(DomainEventInterface $event): void;
}