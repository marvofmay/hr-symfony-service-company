<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\EventLog;

interface LoggableEventInterface
{
    public function getEntityClass(): string;

    public function getData(): array;
}