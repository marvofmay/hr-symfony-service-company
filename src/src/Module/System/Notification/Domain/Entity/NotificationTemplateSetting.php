<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\System\Notification\Domain\Interface\Channel\NotificationChannelInterface;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'notification_template_setting')]
#[ORM\Index(name: 'event_name', columns: ['event_name'])]
#[ORM\Index(name: 'channel_code', columns: ['channel_code'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class NotificationTemplateSetting
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'notification_template_setting';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\Column(type: "string", length: 255)]
    private string $eventName;

    #[ORM\Column(type: "string", length: 50)]
    private string $channelCode;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $content;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isDefault;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isActive;

    private function __construct() {}

    public static function create(
        NotificationEventInterface $event,
        NotificationChannelInterface $channel,
        string $title,
        string $content,
        bool $isDefault,
        bool $isActive
    ): self
    {
        $self = new self();
        $self->uuid = Uuid::uuid4();
        $self->eventName = $event->getName();
        $self->channelCode = $channel->getCode();
        $self->title = $title;
        $self->content = $content;
        $self->isDefault = $isDefault;
        $self->isActive = $isActive;

        return $self;
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
}