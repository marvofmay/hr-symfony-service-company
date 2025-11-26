<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\DTO\Event;

use App\Common\Domain\Abstract\QueryDTOAbstract;

final class ListNotificationEventSettingQueryDTO extends QueryDTOAbstract
{
    public ?string $eventName = null;

    public ?bool $enabled = null;
}
