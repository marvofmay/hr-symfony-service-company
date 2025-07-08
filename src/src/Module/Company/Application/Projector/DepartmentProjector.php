<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
use App\Module\Company\Domain\Event\Company\CompanyDeletedEvent;
use App\Module\Company\Domain\Event\Company\CompanyRestoredEvent;
use App\Module\Company\Domain\Event\Company\CompanyUpdatedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentCreatedEvent;
use App\Module\Company\Domain\Service\Company\CompanyCreator;
use App\Module\Company\Domain\Service\Company\CompanyDeleter;
use App\Module\Company\Domain\Service\Company\CompanyRestorer;
use App\Module\Company\Domain\Service\Company\CompanyUpdater;
use App\Module\Company\Domain\Service\Department\DepartmentCreator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class DepartmentProjector
{
    public function __construct(
        private DepartmentCreator $departmentCreator,
        //private CompanyUpdater  $companyUpdater,
        //private CompanyDeleter  $companyDeleter,
        //private CompanyRestorer $companyRestorer,
    )
    {
    }

    #[AsEventListener(event: DepartmentCreatedEvent::class)]
    public function onDepartmentCreated(DepartmentCreatedEvent $event): void
    {
        $this->departmentCreator->create($event);
    }

    //#[AsEventListener(event: CompanyUpdatedEvent::class)]
    //public function onCompanyUpdated(CompanyUpdatedEvent $event): void
    //{
    //    $this->companyUpdater->update($event);
    //}
    //
    //#[AsEventListener(event: CompanyDeletedEvent::class)]
    //public function onCompanyDeleted(CompanyDeletedEvent $event): void
    //{
    //    $this->companyDeleter->delete($event);
    //}
    //
    //#[AsEventListener(event: CompanyRestoredEvent::class)]
    //public function onCompanyRestored(CompanyRestoredEvent $event): void
    //{
    //    $this->companyRestorer->restore($event);
    //}
}