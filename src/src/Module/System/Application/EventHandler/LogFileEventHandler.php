<?php

namespace App\Module\System\Application\EventHandler;

use App\Module\System\Application\Event\LogFileEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class LogFileEventHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(LogFileEvent $event, string $level = LogLevel::ERROR): void
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