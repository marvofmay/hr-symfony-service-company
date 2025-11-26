<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;

interface NotificationTemplateSettingWriterInterface
{
    public function save(NotificationTemplateSetting $notificationTemplateSetting): void;
    public function delete(NotificationTemplateSetting $notificationTemplateSetting, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;
}
