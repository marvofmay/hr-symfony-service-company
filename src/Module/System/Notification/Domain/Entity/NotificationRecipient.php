<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'notification_recipient')]
#[ORM\Index(name: 'idx_user_uuid', columns: ['user_uuid'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class NotificationRecipient
{
    use TimeStampableTrait;
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: NotificationMessage::class, inversedBy: 'recipients')]
    #[ORM\JoinColumn(name: 'message_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private NotificationMessage $message;

    #[ORM\Column(name: 'user_uuid', type: 'uuid')]
    private UuidInterface $userUUID;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $receivedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $readAt = null;

    private function __construct()
    {
    }

    public static function create(NotificationMessage $message, UuidInterface $userUUID): self
    {
        $self = new self();
        $self->uuid = Uuid::uuid4();
        $self->message = $message;
        $self->userUUID = $userUUID;
        $self->receivedAt = new \DateTime();
        $message->addRecipient($self);

        return $self;
    }

    public function markAsRead(): void
    {
        $this->readAt = new \DateTime();
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getUserUUID(): UuidInterface
    {
        return $this->userUUID;
    }

    public function getReadAt(): ?\DateTimeInterface
    {
        return $this->readAt;
    }

    public function getReceivedAt(): ?\DateTimeInterface
    {
        return $this->receivedAt;
    }
}
