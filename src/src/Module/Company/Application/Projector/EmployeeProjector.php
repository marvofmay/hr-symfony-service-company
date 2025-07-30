<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
use App\Module\Company\Domain\Event\Company\CompanyDeletedEvent;
use App\Module\Company\Domain\Event\Company\CompanyRestoredEvent;
use App\Module\Company\Domain\Event\Company\CompanyUpdatedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeCreatedEvent;
use App\Module\Company\Domain\Service\Company\CompanyCreator;
use App\Module\Company\Domain\Service\Company\CompanyDeleter;
use App\Module\Company\Domain\Service\Company\CompanyRestorer;
use App\Module\Company\Domain\Service\Company\CompanyUpdater;
use App\Module\Company\Domain\Service\Employee\EmployeeCreator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class EmployeeProjector
{
    public function __construct(
        private EmployeeCreator  $employeeCreator,
        //private CompanyUpdater  $companyUpdater,
        //private CompanyDeleter  $companyDeleter,
        //private CompanyRestorer $companyRestorer,
    )
    {
    }

    #[AsEventListener(event: EmployeeCreatedEvent::class)]
    public function onEmployeeCreated(EmployeeCreatedEvent $event): void
    {
        $this->employeeCreator->create($event);
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