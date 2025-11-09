<?php

declare(strict_types=1);

namespace App\Module\System\Domain\DTO\Notification;

use App\Common\Domain\Abstract\QueryDTOAbstract;

final class ListNotificationChannelSettingQueryDTO extends QueryDTOAbstract
{
    public ?string $channel = null;

    public ?bool $enabled = null;
}
