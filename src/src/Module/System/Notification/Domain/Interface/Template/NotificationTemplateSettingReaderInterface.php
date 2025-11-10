<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use Doctrine\Common\Collections\Collection;

interface NotificationTemplateSettingReaderInterface
{
    public function getAll(): Collection;
}
