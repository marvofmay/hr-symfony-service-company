<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface;

interface NotificationChannelSettingCreatorInterface
{
    public function create(NotificationChannelInterface $channel, bool $enabled = true): void;
}