<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\System\Domain\Enum\Notification\NotificationChannelEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: "notification_channel_setting")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class NotificationChannelSetting
{
    use TimeStampableTrait;

    public const string ALIAS = 'notification_channel_setting';

    #[ORM\Id]
    #[ORM\Column(type: "string", length: 50, enumType: NotificationChannelEnum::class)]
    private NotificationChannelEnum $channel;

    #[ORM\Column(type: "boolean")]
    private bool $enabled;

    private function __construct(NotificationChannelEnum $channel, bool $enabled)
    {
        $this->channel = $channel;
        $this->enabled = $enabled;
    }

    public static function create(NotificationChannelEnum $channel, bool $enabled = false): self
    {
        return new self($channel, $enabled);
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getChannel(): NotificationChannelEnum
    {
        return $this->channel;
    }
}