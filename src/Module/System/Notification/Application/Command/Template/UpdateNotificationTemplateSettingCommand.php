<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Command\Template;

use App\Common\Domain\Interface\CommandInterface;

final readonly class UpdateNotificationTemplateSettingCommand implements CommandInterface
{
    public const string EVENT_NAME = 'eventName';
    public const string CHANNEL_CODE = 'channelCode';
    public const string TITLE = 'title';
    public const string CONTENT = 'content';
    public const string SEARCH_DEFAULT = 'searchDefault';
    public const string MARK_AS_ACTIVE = 'markAsActive';

    public function __construct(
        public string $eventName,
        public string $channelCode,
        public string $title,
        public string $content,
        public bool $searchDefault = false,
        public bool $markAsActive = false,
    ) {
    }
}
