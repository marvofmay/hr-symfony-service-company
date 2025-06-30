<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface DomainEventInterface
{
    public function getOccurredAt(): \DateTimeImmutable;
}