<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Employee\EmployeeCreatedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeDeletedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeImportedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeRestoredEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeUpdatedEvent;
use App\Module\Company\Domain\Service\Email\EmailService;
use App\Module\Company\Domain\Service\Employee\EmployeeCreator;
use App\Module\Company\Domain\Service\Employee\EmployeeDeleter;
use App\Module\Company\Domain\Service\Employee\EmployeeRestorer;
use App\Module\Company\Domain\Service\Employee\EmployeeUpdater;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class EmployeeProjector
{
    public function __construct(
        private EmployeeCreator $employeeCreator,
        private EmployeeUpdater $employeeUpdater,
        private EmployeeDeleter $employeeDeleter,
        private EmployeeRestorer $employeeRestorer,
        private EmailService $emailService,
    ) {
    }

    #[AsEventListener(event: EmployeeCreatedEvent::class)]
    public function onEmployeeCreated(EmployeeCreatedEvent $event): void
    {
        $this->employeeCreator->create($event);

        $this->emailService->sendEmail(
            sender: null,
            recipients: $event->emails->toArray(),
            subject: sprintf('Witaj w firmie %s!', $event->firstName->getValue()),
            message: '',
            templateName: 'emails/welcome.html.twig',
            attachments: [],
            context: [
                'firstName' => $event->firstName->getValue(),
                'login' => $event->emails->toArray()[0],
            ],
        );
    }

    #[AsEventListener(event: EmployeeUpdatedEvent::class)]
    public function onEmployeeUpdated(EmployeeUpdatedEvent $event): void
    {
        $this->employeeUpdater->update($event);
    }

    #[AsEventListener(event: EmployeeDeletedEvent::class)]
    public function onEmployeeDeleted(EmployeeDeletedEvent $event): void
    {
        $this->employeeDeleter->delete($event);
    }

    #[AsEventListener(event: EmployeeRestoredEvent::class)]
    public function onEmployeeRestored(EmployeeRestoredEvent $event): void
    {
        $this->employeeRestorer->restore($event);
    }

    #[AsEventListener(event: EmployeeImportedEvent::class)]
    public function onEmployeeMultipleImported(EmployeeImportedEvent $event): void
    {
        // ToDo: save notification about DONE import - immediately
        // ToDo: if notification for import employees is turned on by employee in employee settings
    }
}
