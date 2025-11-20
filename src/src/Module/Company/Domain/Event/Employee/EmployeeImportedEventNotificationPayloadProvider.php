<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Employee;

use App\Common\Domain\Interface\NotifiableEventInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Domain\Trait\ClassNameExtractorTrait;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventPayloadProviderInterface;

final readonly class EmployeeImportedEventNotificationPayloadProvider  implements NotificationEventPayloadProviderInterface
{
    use ClassNameExtractorTrait;

    public function __construct(private ImportReaderInterface $importReaderRepository, private MessageService  $messageService)
    {
    }

    public function supports(string $notifiableEventName): bool
    {
        return $notifiableEventName === $this->getShortClassName(EmployeeImportedEvent::class);
    }

    public function provide(NotifiableEventInterface $notifiableEvent): array
    {
        $import = $this->importReaderRepository->getImportByUuid($notifiableEvent->importUUID);
        $payload = [
            'importKind' => $this->messageService->get('import.kind.employee', [], 'imports'),
            'importStatus' => $this->messageService->get('import.status.'. $import->getStatus()->value, [], 'imports'),
        ];

        $recipientUUIDs = [$import->getUser()->getUuid()];

        return [$payload, $recipientUUIDs];
    }
}