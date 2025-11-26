<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\CommandHandler\Channel;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\System\Notification\Application\Command\Channel\UpdateNotificationChannelSettingsCommand;
use App\Module\System\Notification\Application\Event\Channel\NotificationChannelSettingsUpdatedEvent;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Channel\NotificationChannelSettingUpdater;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateNotificationChannelSettingsCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NotificationChannelSettingUpdater $notificationChannelSettingUpdater,
        private readonly NotificationChannelSettingReaderInterface $notificationChannelSettingReader,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageService $messageService,
    ) {
    }

    public function __invoke(UpdateNotificationChannelSettingsCommand $command): void
    {
        $currentNotificationChannels = $this->notificationChannelSettingReader->getAll();
        if ($currentNotificationChannels->isEmpty()) {
            throw new \Exception($this->messageService->get('notification.channels.notFound', [], 'notifications'), Response::HTTP_NOT_FOUND);
        }
        foreach ($currentNotificationChannels as $notificationChannel) {
            if (in_array($notificationChannel->getChannelCode(), $command->channelCodes, true)) {
                $this->notificationChannelSettingUpdater->update($notificationChannel);
            } else {
                $this->notificationChannelSettingUpdater->update($notificationChannel, false);
            }
        }

        $this->eventDispatcher->dispatch(new NotificationChannelSettingsUpdatedEvent([
            UpdateNotificationChannelSettingsCommand::CHANNEL_CODES => $command->channelCodes,
        ]));
    }
}