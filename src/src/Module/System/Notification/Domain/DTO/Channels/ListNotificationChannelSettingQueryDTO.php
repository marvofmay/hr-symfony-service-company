<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\DTO\Channels;

use App\Common\Domain\Abstract\QueryDTOAbstract;

final class ListNotificationChannelSettingQueryDTO extends QueryDTOAbstract
{
    public ?string $channelCode = null;

    public ?bool $enabled = null;
}
