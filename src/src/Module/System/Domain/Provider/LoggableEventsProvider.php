<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Provider;

use App\Module\Company\Application\Event\Industry\IndustryCreatedEvent;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;

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
            IndustryCreatedEvent::class,
        ];
    }
}
