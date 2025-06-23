<?php

namespace App\Module\System\Application\EventHandler;

use App\Module\System\Application\Event\LogEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class LogEventHandler
{
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(LogEvent $event): void
    {
        $this->logger->info($event->message);
    }
}