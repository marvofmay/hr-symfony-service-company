<?php

namespace App\Module\System\Application\EventSubscriber;

use App\Common\Domain\Interface\NotifiableEventInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Domain\Trait\ClassNameExtractorTrait;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Notification\Domain\Interface\Message\NotificationResolveInterface;
use Psr\Log\LoggerInterface;

final readonly class GenericEventNotifierSubscriber
{
    use ClassNameExtractorTrait;

    public function __construct(
        private NotificationResolveInterface $notificationResolve,
        private LoggerInterface $logger,
        private ImportReaderInterface $importReaderRepository,
        private MessageService $messageService,
    )
    {
    }

    public function onNotifiableEvent(NotifiableEventInterface $event): void
    {
        // ToDo:: another, independent listener for websocket to send info about new notification REACT
        $import = $this->importReaderRepository->getImportByUUID($event->importUUID);
        $this->notificationResolve->resolve(
            $this->getShortClassName($event::class),
            [$import->getUser()->getUUID()],
            ['status' => $this->messageService->get('import.status.'. $import->getStatus()->value, [], 'imports')]
        );
    }
}
