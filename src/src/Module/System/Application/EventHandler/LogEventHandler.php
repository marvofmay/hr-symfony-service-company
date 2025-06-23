<?php

namespace App\Module\System\Application\EventHandler;

use App\Module\System\Application\Event\LogEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final readonly class LogEventHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(LogEvent $event, string $level = LogLevel::ERROR): void
    {
        match ($level) {
            LogLevel::DEBUG => $this->logger->debug($event->message),
            LogLevel::NOTICE => $this->logger->notice($event->message),
            LogLevel::WARNING => $this->logger->warning($event->message),
            LogLevel::ERROR => $this->logger->error($event->message),
            LogLevel::CRITICAL => $this->logger->critical($event->message),
            LogLevel::ALERT => $this->logger->alert($event->message),
            LogLevel::EMERGENCY => $this->logger->emergency($event->message),
            default => $this->logger->info($event->message),
        };
    }
}