<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use Doctrine\Common\Collections\Collection;

interface NotificationTemplateSettingReaderInterface
{
    public function getAll(): Collection;
    public function getByEventNameChannelCodeAndDefault(string $eventName, string $channelCode, bool $searchDefault): ?NotificationTemplateSetting;
    public function getActiveByEventName(string $eventName): Collection;
}
