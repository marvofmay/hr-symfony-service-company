<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Channel;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notification.channel')]
interface NotificationChannelInterface
{
    public static function getChanelCode(): string;
    public function getCode(): string;

    public function getLabel(): string;
}
