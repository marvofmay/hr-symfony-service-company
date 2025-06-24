<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Provider;

use App\Module\Company\Application\Event\Industry\IndustryCreatedEvent;
use App\Module\Company\Application\Event\Role\RoleAssignedAccessesEvent;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Application\Event\Role\RoleImportedEvent;
use App\Module\Company\Application\Event\Role\RoleListedEvent;
use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Application\Event\Role\RoleViewedEvent;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionInterface;

final class LoggableEventsProvider
{
    public static function getEvents(): array
    {
        return array_fill_keys(self::getEventClasses(), 'onLoggableEvent');
    }

    private static function getEventClasses(): array
    {
        return [
            RoleCreatedEvent::class,
            RoleUpdatedEvent::class,
            RoleDeletedEvent::class,
            RoleViewedEvent::class,
            RoleListedEvent::class,
            RoleMultipleDeletedEvent::class,
            RoleImportedEvent::class,
            RoleAssignedAccessesEvent::class,
            RoleAccessPermissionInterface::class,
            IndustryCreatedEvent::class,
        ];
    }
}
