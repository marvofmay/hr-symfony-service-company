<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Command\Channel;

use App\Common\Domain\Interface\CommandInterface;

class UpdateNotificationChannelSettingsCommand implements CommandInterface
{
    public const string CHANNEL_CODES = 'channelCodes';

    public function __construct(public array $channelCodes = []) {}
}