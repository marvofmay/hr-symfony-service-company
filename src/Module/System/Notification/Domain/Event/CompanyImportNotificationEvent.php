<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Event;

use App\Common\Domain\Trait\ClassNameExtractorTrait;
use App\Module\Company\Domain\Event\Company\CompanyImportedEvent;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;

class CompanyImportNotificationEvent implements NotificationEventInterface
{
    use ClassNameExtractorTrait;

    public function getName(): string
    {
        return $this->getShortClassName(CompanyImportedEvent::class);
    }

    public function getLabel(): string
    {
        return 'notification.event.import.company';
    }
}
