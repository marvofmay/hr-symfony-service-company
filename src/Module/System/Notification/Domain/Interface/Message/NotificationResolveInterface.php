<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Message;

use App\Common\Domain\Interface\NotifiableEventInterface;

interface NotificationResolveInterface
{
    public function resolve(NotifiableEventInterface $notifiableEvent): void;
    public function renderTemplate(string $template, array $payload): string;
}