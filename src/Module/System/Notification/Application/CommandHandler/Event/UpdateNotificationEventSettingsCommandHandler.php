<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\CommandHandler\Event;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\System\Notification\Application\Command\Event\UpdateNotificationEventSettingsCommand;
use App\Module\System\Notification\Application\Event\Event\NotificationEventSettingsUpdatedEvent;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Event\NotificationEventSettingUpdater;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateNotificationEventSettingsCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NotificationEventSettingUpdater $notificationEventSettingUpdater,
        private readonly NotificationEventSettingReaderInterface $notificationEventSettingReader,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageService $messageService,
    ) {
    }

    public function __invoke(UpdateNotificationEventSettingsCommand $command): void
    {
        $currentNotificationEvents = $this->notificationEventSettingReader->getAll();
        if ($currentNotificationEvents->isEmpty()) {
            throw new \Exception($this->messageService->get('notification.events.notFound', [], 'notifications'), Response::HTTP_NOT_FOUND);
        }
        foreach ($currentNotificationEvents as $notificationEvent) {
            if (in_array($notificationEvent->getEventName(), $command->eventNames, true)) {
                $this->notificationEventSettingUpdater->update($notificationEvent);
            } else {
                $this->notificationEventSettingUpdater->update($notificationEvent, false);
            }
        }

        $this->eventDispatcher->dispatch(new NotificationEventSettingsUpdatedEvent([
            UpdateNotificationEventSettingsCommand::EVENT_NAMES => $command->eventNames,
        ]));
    }
}
