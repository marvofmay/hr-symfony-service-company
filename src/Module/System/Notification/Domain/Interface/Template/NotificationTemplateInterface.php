<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Template;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notification.template')]
interface NotificationTemplateInterface
{
    public function isDefault(): bool;
    public function getTitle(): string;

    public function getContent(): string;
}
