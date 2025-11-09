<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Notification;

use App\Common\Domain\Interface\CommandInterface;

class UpdateNotificationChannelSettingsCommand implements CommandInterface
{
    public const string CHANNELS = 'channels';

    public function __construct(public array $channels = []) {}
}