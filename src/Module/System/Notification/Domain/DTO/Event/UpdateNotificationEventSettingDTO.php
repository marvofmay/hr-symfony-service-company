<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\DTO\Event;

use App\Module\System\Notification\Domain\Validator\Constraints\Event\ValidNotificationEvents;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateNotificationEventSettingDTO
{
    #[Assert\NotBlank(message: 'notification.event.required')]
    #[ValidNotificationEvents(message: ['eventNotExists' => 'notification.event.invalidChoice', 'domain' => 'notifications'])]
    public array $eventNames = [];

}
