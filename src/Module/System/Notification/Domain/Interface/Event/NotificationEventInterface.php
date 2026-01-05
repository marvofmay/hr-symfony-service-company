<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Event;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notification.event')]
interface NotificationEventInterface
{
    public function getName(): string;

    public function getLabel(): string;
}
