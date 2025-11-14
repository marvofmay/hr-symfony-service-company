<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\WebSocket;

use Ramsey\Uuid\UuidInterface;

interface WebSocketPusherInterface
{
    public function pushToUser(UuidInterface $userUUID, string $event, array $payload): void;
}