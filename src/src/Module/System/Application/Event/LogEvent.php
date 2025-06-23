<?php

declare(strict_types=1);

namespace App\Module\System\Application\Event;

use Psr\Log\LogLevel;

final readonly class LogEvent
{
    public function __construct(public string $message, public string $level = LogLevel::ALERT) {}
}