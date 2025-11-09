<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\System\Application\Event\Notification\NotificationChannelSettingsCreatedEvent;
use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;
use App\Module\System\Domain\Interface\Notification\NotificationChannelSettingReaderInterface;
use App\Module\System\Domain\Service\Notification\NotificationChannelSettingCreator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-record-to-notification-channel-setting-table')]
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
        private readonly EventDispatcherInterface $eventDispatcher
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
            fn($setting) => $setting->getChannel()->value,
            $this->notificationChannelSettingReader->getAll()->toArray()
        );

        $channelsToPersist = [];
        foreach (NotificationChannelEnum::cases() as $enum) {
            if (!in_array($enum->value, $existingChannelValues, true)) {
                $this->notificationChannelSettingCreator->create($enum);
                $this->eventDispatcher->dispatch(new NotificationChannelSettingsCreatedEvent([
                    'channel' => $enum->value,
                ]));
                $channelsToPersist[] = $enum->value;
            }
        }

        if (!empty($channelsToPersist)) {
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $channelsToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
