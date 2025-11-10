<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Event;

interface NotificationEventSettingCreatorInterface
{
    public function create(NotificationEventInterface $event, bool $enabled = true): void;
}