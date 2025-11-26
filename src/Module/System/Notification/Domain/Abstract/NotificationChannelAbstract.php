<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Abstract;

use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;

abstract class NotificationChannelAbstract implements NotificationChannelInterface
{
    public static function getChanelCode(): string
    {
        return new static()->getCode();
    }

    abstract public function getCode(): string;
    abstract public function getLabel(): string;
}