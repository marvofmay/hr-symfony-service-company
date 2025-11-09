<?php

declare(strict_types=1);

namespace App\Module\System\Domain\DTO\Notification;

use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateNotificationChannelSettingDTO
{
    #[Assert\All([
        new Assert\Choice(
            choices: NotificationChannelEnum::VALUES,
            message: 'notification.channel.invalidChoice'
        )
    ])]
    #[Assert\NotBlank(message: 'notification.channel.required')]
    public array $channels = [];
}
