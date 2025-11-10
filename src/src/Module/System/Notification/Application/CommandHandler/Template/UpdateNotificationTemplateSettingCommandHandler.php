<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\CommandHandler\Template;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\System\Notification\Application\Command\Template\UpdateNotificationTemplateSettingCommand;
use App\Module\System\Notification\Application\Event\Template\NotificationTemplateSettingUpdatedEvent;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Template\NotificationTemplateSettingUpdater;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateNotificationTemplateSettingCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly NotificationTemplateSettingUpdater $notificationTemplateSettingUpdater,
        private readonly NotificationTemplateSettingReaderInterface $notificationTemplateSettingReader,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageService $messageService,
    ) {
    }

    public function __invoke(UpdateNotificationTemplateSettingCommand $command): void
    {
        $this->eventDispatcher->dispatch(new NotificationTemplateSettingUpdatedEvent([
            UpdateNotificationTemplateSettingCommand::EVENT_NAME => $command->eventName,
            UpdateNotificationTemplateSettingCommand::CHANNEL_CODE => $command->channelCode,
            UpdateNotificationTemplateSettingCommand::TITLE => $command->title,
            UpdateNotificationTemplateSettingCommand::CONTENT => $command->content,
            UpdateNotificationTemplateSettingCommand::IS_DEFAULT => $command->isDefault,
        ]));
    }
}