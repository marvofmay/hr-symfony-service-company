<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Department\DepartmentCreatedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentDeletedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentMultipleImportedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentRestoredEvent;
use App\Module\Company\Domain\Event\Department\DepartmentUpdatedEvent;
use App\Module\Company\Domain\Service\Department\DepartmentCreator;
use App\Module\Company\Domain\Service\Department\DepartmentDeleter;
use App\Module\Company\Domain\Service\Department\DepartmentRestorer;
use App\Module\Company\Domain\Service\Department\DepartmentUpdater;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class DepartmentProjector
{
    public function __construct(
        private DepartmentCreator $departmentCreator,
        private DepartmentUpdater $departmentUpdater,
        private DepartmentDeleter $departmentDeleter,
        private DepartmentRestorer $departmentRestorer,
    ) {
    }

    #[AsEventListener(event: DepartmentCreatedEvent::class)]
    public function onDepartmentCreated(DepartmentCreatedEvent $event): void
    {
        $this->departmentCreator->create($event);
    }

    #[AsEventListener(event: DepartmentUpdatedEvent::class)]
    public function onDepartmentUpdated(DepartmentUpdatedEvent $event): void
    {
        $this->departmentUpdater->update($event);
    }

    #[AsEventListener(event: DepartmentDeletedEvent::class)]
    public function onDepartmentDeleted(DepartmentDeletedEvent $event): void
    {
        $this->departmentDeleter->delete($event);
    }

    #[AsEventListener(event: DepartmentRestoredEvent::class)]
    public function onDepartmentRestored(DepartmentRestoredEvent $event): void
    {
        $this->departmentRestorer->restore($event);
    }

    #[AsEventListener(event: DepartmentMultipleImportedEvent::class)]
    public function onDepartmentMultipleImported(DepartmentMultipleImportedEvent $event): void
    {
        // ToDo: save notification about DONE import - immediately
        // ToDo: if notification for import departments is turned on by employee in employee settings
    }
}
