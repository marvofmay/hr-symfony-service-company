<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Provider;

use App\Module\Company\Application\Event\Company\CompanyListedEvent;
use App\Module\Company\Application\Event\Company\CompanyViewedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeCreatedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeDeletedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeImportedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeListedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeMultipleDeletedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeUpdatedEvent;
use App\Module\Company\Application\Event\ContractType\ContractTypeViewedEvent;
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
        return array_merge(
            self::getCompanyEventClasses(),
            self::getRoleEventClasses(),
            self::getIndustryEventClasses(),
            self::getPositionEventClasses(),
            self::getContractTypeEventsClasses()
        );
    }

    private static function getCompanyEventClasses(): array
    {
        return [
            CompanyViewedEvent::class,
            CompanyListedEvent::class,
        ];
    }

    private static function getRoleEventClasses(): array
    {
        return [
            RoleAssignedAccessesEvent::class,
            RoleAssignedPermissionsEvent::class,
            RoleCreatedEvent::class,
            RoleUpdatedEvent::class,
            RoleDeletedEvent::class,
            RoleViewedEvent::class,
            RoleListedEvent::class,
            RoleMultipleDeletedEvent::class,
            RoleImportedEvent::class,
        ];
    }

    private static function getIndustryEventClasses(): array
    {
        return [
            IndustryCreatedEvent::class,
            IndustryUpdatedEvent::class,
            IndustryDeletedEvent::class,
            IndustryViewedEvent::class,
            IndustryListedEvent::class,
            IndustryMultipleDeletedEvent::class,
            IndustryImportedEvent::class,
        ];
    }

    private static function getPositionEventClasses(): array
    {
        return [
            PositionCreatedEvent::class,
            PositionUpdatedEvent::class,
            PositionDeletedEvent::class,
            PositionViewedEvent::class,
            PositionListedEvent::class,
            PositionMultipleDeletedEvent::class,
            PositionImportedEvent::class,
        ];
    }

    public static function getContractTypeEventsClasses(): array
    {
        return [
            ContractTypeCreatedEvent::class,
            ContractTypeUpdatedEvent::class,
            ContractTypeDeletedEvent::class,
            ContractTypeViewedEvent::class,
            ContractTypeListedEvent::class,
            ContractTypeMultipleDeletedEvent::class,
            ContractTypeImportedEvent::class,
        ];
    }
}
