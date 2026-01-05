<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'notification_message')]
#[ORM\Index(name: 'event_name', columns: ['event_name'])]
#[ORM\Index(name: 'channel_code', columns: ['channel_code'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class NotificationMessage
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'notification_message';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: NotificationEventSetting::class)]
    #[ORM\JoinColumn(name: 'event_name', referencedColumnName: 'event_name', nullable: false, onDelete: 'CASCADE')]
    private NotificationEventSetting $event;

    #[ORM\ManyToOne(targetEntity: NotificationChannelSetting::class)]
    #[ORM\JoinColumn(name: 'channel_code', referencedColumnName: 'channel_code', nullable: false, onDelete: 'CASCADE')]
    private NotificationChannelSetting $channel;

    #[ORM\ManyToOne(targetEntity: NotificationTemplateSetting::class)]
    #[ORM\JoinColumn(name: 'template_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'SET NULL')]
    private ?NotificationTemplateSetting $template;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    #[ORM\OneToMany(targetEntity: NotificationRecipient::class, mappedBy: 'message', cascade: ['persist', 'remove'])]
    private Collection $recipients;

    private function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    public static function create(
        NotificationEventSetting $event,
        NotificationChannelSetting $channel,
        ?NotificationTemplateSetting $template,
        string $title,
        string $content
    ): self {
        $self = new self();
        $self->uuid = Uuid::uuid4();
        $self->event = $event;
        $self->channel = $channel;
        $self->template = $template;
        $self->title = $title;
        $self->content = $content;

        return $self;
    }

    public function markAsSent(): void
    {
        $this->sentAt = new \DateTime();
    }

    public function addRecipient(NotificationRecipient $recipient): void
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
