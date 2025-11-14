<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\WebSocket;

use App\Module\System\Domain\Interface\WebSocket\WebSocketPusherInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePusher implements WebSocketPusherInterface
{
    public function __construct(private HubInterface $hub) {}

    public function pushToUser(UuidInterface $userUUID, string $event, array $payload): void
    {
        $update = new Update(
            topics: ['user.'.$userUUID->toString()],
            data: json_encode([
                'event' => $event,
                'payload' => $payload,
            ])
        );

        $this->hub->publish($update);
    }
}