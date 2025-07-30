<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Employee\EmployeeCreatedEvent;
use App\Module\Company\Domain\Service\Employee\EmployeeCreator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class EmployeeProjector
{
    public function __construct(
        private EmployeeCreator  $employeeCreator,
        //private EmployeeUpdater  $employeeUpdater,
        //private EmployeeDeleter  $employeeDeleter,
        //private EmployeeRestorer $employeeRestorer,
    )
    {
    }

    #[AsEventListener(event: EmployeeCreatedEvent::class)]
    public function onEmployeeCreated(EmployeeCreatedEvent $event): void
    {
        $this->employeeCreator->create($event);
    }

    //#[AsEventListener(event: EmployeeUpdatedEvent::class)]
    //public function onEmployeeUpdated(EmployeeUpdatedEvent $event): void
    //{
    //    $this->companyUpdater->update($event);
    //}
    //
    //#[AsEventListener(event: EmployeeDeletedEvent::class)]
    //public function onEmployeeDeleted(EmployeeDeletedEvent $event): void
    //{
    //    $this->companyDeleter->delete($event);
    //}
    //
    //#[AsEventListener(event: EmployeeRestoredEvent::class)]
    //public function onEmployeeRestored(EmployeeRestoredEvent $event): void
    //{
    //    $this->companyRestorer->restore($event);
    //}
}