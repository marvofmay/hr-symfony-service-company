<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Console;

use App\Module\System\Notification\Application\Event\Channel\NotificationChannelSettingsCreatedEvent;
use App\Module\System\Notification\Domain\Factory\NotificationChannelFactory;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Channel\NotificationChannelSettingCreator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AsCommand(name: 'app:add-record-to-notification-channel-setting-table')]
#[AutoconfigureTag('app.command.initialize-system-default-data', ['priority' => -180])]
final class AddRecordToNotificationChannelSettingTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to "notification_channel_setting" table';
    private const string HELP = 'This command ensures that all NotificationChannelEnum values exist in the "notification_channel_setting table"';
    private const string SUCCESS_MESSAGE = '"notification_channel_setting" table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing notification channels';
    private const string INFO_NO_ADDED_MESSAGE = 'No new notification channels to add';

    public function __construct(
        private readonly NotificationChannelSettingReaderInterface $notificationChannelSettingReader,
        private readonly NotificationChannelSettingCreator $notificationChannelSettingCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly NotificationChannelFactory $notificationChannelFactory,
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
        $output->writeln('Checking and updating "notification_channel_setting" table...');
        $existingChannelValues = array_map(
            fn($setting) => $setting->getChannelCode(),
            $this->notificationChannelSettingReader->getAll()->toArray()
        );

        $channelCodesToPersist = [];
        $channels = $this->notificationChannelFactory->all();
        foreach ($channels as $channel) {
            if (!in_array($channel->getCode(), $existingChannelValues, true)) {
                $this->notificationChannelSettingCreator->create($channel);
                $this->eventDispatcher->dispatch(new NotificationChannelSettingsCreatedEvent([
                    'channelCode' => $channel->getCode(),
                ]));
                $channelCodesToPersist[] = $channel->getCode();
            }
        }

        if (!empty($channelCodesToPersist)) {
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $channelCodesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<comment>%s</comment>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
