<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventSubscriber;

use App\Common\Domain\Interface\NotifiableEventInterface;
use App\Common\Domain\Trait\ClassNameExtractorTrait;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Domain\Interface\WebSocket\WebSocketPusherInterface;
use Psr\Log\LoggerInterface;

final readonly class RealTimeEventNotifierSubscriber
{
    use ClassNameExtractorTrait;

    public function __construct(
        private WebSocketPusherInterface   $websocketPusher,
        private ImportReaderInterface      $importReaderRepository,
        private LoggerInterface            $logger,
    ) {}

    public function onNotifiableEvent(NotifiableEventInterface $event): void
    {
        try {
            $import = $this->importReaderRepository->getImportByUUID($event->importUUID);

            // payload pushowany do Reacta
            $message = [
                'event'   => $this->getShortClassName($event::class),
                'user'    => $import->getUser()->getUUID()->toString(),
                'status'  => $import->getStatus()->value,
                'import'  => $import->getUUID()->toString(),
                'created' => new \DateTime(),
            ];

            // wysłanie real-time
            $this->websocketPusher->pushToUser(
                userUUID: $import->getUser()->getUUID()->toString(),
                event: 'import.updated',
                payload: $message
            );
        } catch (\Throwable $e) {
            // żeby websocket nigdy nie blokował logiki domenowej
            $this->logger->error('RealTime notification failed', [
                'exception' => $e,
            ]);
        }
    }
}