<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Console;

use App\Module\System\Notification\Application\Event\Template\NotificationTemplateSettingCreatedEvent;
use App\Module\System\Notification\Domain\Enum\NotificationChannelSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Enum\NotificationEventSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Enum\NotificationTemplateSettingEntityFieldEnum;
use App\Module\System\Notification\Domain\Factory\NotificationChannelFactory;
use App\Module\System\Notification\Domain\Factory\NotificationEventFactory;
use App\Module\System\Notification\Domain\Factory\NotificationTemplateFactory;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Template\NotificationTemplateSettingCreator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-record-to-notification-template-setting-table')]
final class AddRecordToNotificationTemplateSettingTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to "notification_template_setting" table';
    private const string HELP = 'This command ensures that all NotificationTemplateEnum values exist in the "notification_template_setting table"';
    private const string SUCCESS_MESSAGE = '"notification_template_setting" table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing notification templates';
    private const string INFO_NO_ADDED_MESSAGE = 'No new notification templates to add';

    public function __construct(
        private readonly NotificationTemplateSettingReaderInterface $notificationTemplateSettingReader,
        private readonly NotificationTemplateSettingCreator $notificationTemplateSettingCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly NotificationTemplateFactory $notificationTemplateFactory,
        private readonly NotificationChannelFactory $notificationChannelFactory,
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
        $output->writeln('Checking and updating "notification_template_setting" table...');

        $existingTemplateValues = array_map(
            fn($setting) => sprintf(
                '%s|%s|%s',
                $setting->getEventName(),
                $setting->getChannelCode(),
                $setting->isDefault() ? 'default' : 'custom'
            ),
            $this->notificationTemplateSettingReader->getAll()->toArray()
        );

        $existingTemplateValues = array_flip($existingTemplateValues);
        $templateCodesToPersist = [];

        $events = $this->notificationEventFactory->all();
        $channels = $this->notificationChannelFactory->all();
        $templates = $this->notificationTemplateFactory->all();

        foreach ($events as $event) {
            foreach ($channels as $channel) {
                foreach ($templates as $template) {
                    $scope = $template->isDefault() ? 'default' : 'custom';
                    $key = sprintf('%s|%s|%s', $event->getName(), $channel->getCode(), $scope);

                    if (isset($existingTemplateValues[$key])) {
                        continue;
                    }

                    $this->notificationTemplateSettingCreator->create(
                        $event,
                        $channel,
                        $template->getTitle(),
                        $template->getContent(),
                        $template->isDefault(),
                        $template->isDefault()
                    );

                    $this->eventDispatcher->dispatch(new NotificationTemplateSettingCreatedEvent([
                        NotificationEventSettingEntityFieldEnum::EVENT_NAME->value => $event->getName(),
                        NotificationChannelSettingEntityFieldEnum::CHANNEL_CODE->value => $channel->getCode(),
                        NotificationTemplateSettingEntityFieldEnum::TITLE->value => $template->getTitle(),
                        NotificationTemplateSettingEntityFieldEnum::CONTENT->value => $template->getContent(),
                        NotificationTemplateSettingEntityFieldEnum::IS_DEFAULT->value => $template->isDefault(),
                        NotificationTemplateSettingEntityFieldEnum::IS_ACTIVE->value => $template->isDefault(),
                    ]));

                    $templateCodesToPersist[] = $key;
                    $existingTemplateValues[$key] = true;
                }
            }
        }

        if (!empty($templateCodesToPersist)) {
            $output->writeln(sprintf(
                '<info>%s:</info> %s',
                self::INFO_ADDED_MESSAGE,
                implode(', ', $templateCodesToPersist)
            ));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<comment>%s</comment>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
