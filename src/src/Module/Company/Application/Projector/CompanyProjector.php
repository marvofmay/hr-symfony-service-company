<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
use App\Module\Company\Domain\Event\Company\CompanyDeletedEvent;
use App\Module\Company\Domain\Event\Company\CompanyImportedEvent;
use App\Module\Company\Domain\Event\Company\CompanyRestoredEvent;
use App\Module\Company\Domain\Event\Company\CompanyUpdatedEvent;
use App\Module\Company\Domain\Service\Company\CompanyCreator;
use App\Module\Company\Domain\Service\Company\CompanyDeleter;
use App\Module\Company\Domain\Service\Company\CompanyRestorer;
use App\Module\Company\Domain\Service\Company\CompanyUpdater;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class CompanyProjector
{
    public function __construct(
        private CompanyCreator $companyCreator,
        private CompanyUpdater $companyUpdater,
        private CompanyDeleter $companyDeleter,
        private CompanyRestorer $companyRestorer,
    ) {
    }

    #[AsEventListener(event: CompanyCreatedEvent::class)]
    public function onCompanyCreated(CompanyCreatedEvent $event): void
    {
        $this->companyCreator->create($event);
    }

    #[AsEventListener(event: CompanyUpdatedEvent::class)]
    public function onCompanyUpdated(CompanyUpdatedEvent $event): void
    {
        $this->companyUpdater->update($event);
    }

    #[AsEventListener(event: CompanyDeletedEvent::class)]
    public function onCompanyDeleted(CompanyDeletedEvent $event): void
    {
        $this->companyDeleter->delete($event);
    }

    #[AsEventListener(event: CompanyRestoredEvent::class)]
    public function onCompanyRestored(CompanyRestoredEvent $event): void
    {
        $this->companyRestorer->restore($event);
    }

    #[AsEventListener(event: CompanyImportedEvent::class)]
    public function onCompanyMultipleImported(CompanyImportedEvent $event): void
    {
        // ToDo:: save notification about DONE import - immediately
        // ToDo:: if notification for import companies is turned on by employee in employee settings
    }
}
