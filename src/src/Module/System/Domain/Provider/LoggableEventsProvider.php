<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Provider;

use App\Module\Company\Application\Event\Industry\IndustryCreatedEvent;
use App\Module\Company\Application\Event\Industry\IndustryDeletedEvent;
use App\Module\Company\Application\Event\Industry\IndustryImportedEvent;
use App\Module\Company\Application\Event\Industry\IndustryListedEvent;
use App\Module\Company\Application\Event\Industry\IndustryMultipleDeletedEvent;
use App\Module\Company\Application\Event\Industry\IndustryUpdatedEvent;
use App\Module\Company\Application\Event\Industry\IndustryViewedEvent;
use App\Module\Company\Application\Event\Position\PositionCreatedEvent;
use App\Module\Company\Application\Event\Position\PositionDeletedEvent;
use App\Module\Company\Application\Event\Position\PositionImportedEvent;
use App\Module\Company\Application\Event\Position\PositionListedEvent;
use App\Module\Company\Application\Event\Position\PositionMultipleDeletedEvent;
use App\Module\Company\Application\Event\Position\PositionUpdatedEvent;
use App\Module\Company\Application\Event\Position\PositionViewedEvent;
use App\Module\Company\Application\Event\Role\RoleAssignedAccessesEvent;
use App\Module\Company\Application\Event\Role\RoleAssignedPermissionsEvent;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Application\Event\Role\RoleImportedEvent;
use App\Module\Company\Application\Event\Role\RoleListedEvent;
use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Application\Event\Role\RoleViewedEvent;

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
            RoleAssignedPermissionsEvent::class,
            IndustryCreatedEvent::class,
            IndustryUpdatedEvent::class,
            IndustryDeletedEvent::class,
            IndustryViewedEvent::class,
            IndustryListedEvent::class,
            IndustryMultipleDeletedEvent::class,
            IndustryImportedEvent::class,
            PositionCreatedEvent::class,
            PositionUpdatedEvent::class,
            PositionDeletedEvent::class,
            PositionViewedEvent::class,
            PositionListedEvent::class,
            PositionMultipleDeletedEvent::class,
            PositionImportedEvent::class,
        ];
    }
}
