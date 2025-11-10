<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\DTO;

use App\Module\System\Notification\Domain\Validator\Constraints\ValidNotificationChannels;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateNotificationChannelSettingDTO
{
    #[Assert\NotBlank(message: 'notification.channel.required')]
    #[ValidNotificationChannels(message: ['channelNotExists' => 'notification.channel.invalidChoice', 'domain' => 'notifications'])]
    public array $channelCodes = [];

}
