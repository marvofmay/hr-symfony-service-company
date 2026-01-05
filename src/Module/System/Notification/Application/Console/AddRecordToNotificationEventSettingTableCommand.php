<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Console;

use App\Module\System\Notification\Application\Event\Event\NotificationEventSettingsCreatedEvent;
use App\Module\System\Notification\Domain\Factory\NotificationEventFactory;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Event\NotificationEventSettingCreator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AsCommand(name: 'app:add-record-to-notification-event-setting-table')]
#[AutoconfigureTag('app.command.initialize-system-default-data', ['priority' => -190])]
final class AddRecordToNotificationEventSettingTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to "notification_event_setting" table';
    private const string HELP = 'This command ensures that all NotificationEventEnum values exist in the "notification_event_setting table"';
    private const string SUCCESS_MESSAGE = '"notification_event_setting" table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing notification events';
    private const string INFO_NO_ADDED_MESSAGE = 'No new notification events to add';

    public function __construct(
        private readonly NotificationEventSettingReaderInterface $notificationEventSettingReader,
        private readonly NotificationEventSettingCreator $notificationEventSettingCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly NotificationEventFactory $notificationEventFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->setHelp(self::HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Checking and updating "notification_event_setting" table...');
        $existingEventValues = array_map(
            fn ($setting) => $setting->getEventName(),
            $this->notificationEventSettingReader->getAll()->toArray()
        );

        $eventNamesToPersist = [];
        $events = $this->notificationEventFactory->all();
        foreach ($events as $event) {
            if (!in_array($event->getName(), $existingEventValues, true)) {
                $this->notificationEventSettingCreator->create($event);
                $this->eventDispatcher->dispatch(new NotificationEventSettingsCreatedEvent([
                    'eventName' => $event->getName(),
                ]));
                $eventNamesToPersist[] = $event->getName();
            }
        }

        if (!empty($eventNamesToPersist)) {
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $eventNamesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<comment>%s</comment>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
