<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface AggregateRootInterface
{
    public function pullEvents(): array;

    public static function reconstituteFromHistory(array $events): static;
}
