<?php

declare(strict_types=1);

namespace App\Module\System\Application\CommandHandler\Notification;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\System\Application\Command\Notification\UpdateNotificationChannelSettingsCommand;
use App\Module\System\Application\Event\Notification\NotificationChannelSettingsUpdatedEvent;
use App\Module\System\Domain\Interface\Notification\NotificationChannelSettingReaderInterface;
use App\Module\System\Domain\Service\Notification\NotificationChannelSettingUpdater;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateNotificationChannelSettingsCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NotificationChannelSettingUpdater $notificationChannelSettingUpdater,
        private readonly NotificationChannelSettingReaderInterface $notificationChannelSettingReader,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(UpdateNotificationChannelSettingsCommand $command): void
    {
        $currentNotificationChannels = $this->notificationChannelSettingReader->getAll();
        foreach ($currentNotificationChannels as $notificationChannel) {
            if (in_array($notificationChannel->getChannel()->value, $command->channels, true)) {
                $this->notificationChannelSettingUpdater->update($notificationChannel);
            } else {
                $this->notificationChannelSettingUpdater->update($notificationChannel, false);
            }
        }

        $this->eventDispatcher->dispatch(new NotificationChannelSettingsUpdatedEvent([
            UpdateNotificationChannelSettingsCommand::CHANNELS => $command->channels,
        ]));
    }
}