<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Message;

interface NotificationResolveInterface
{
    public function resolve(string $eventName, array $recipientUUIDs, array $payload = []): void;
    public function renderTemplate(string $template, array $payload): string;
}