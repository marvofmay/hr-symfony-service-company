<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\System\Notification\Domain\Interface\Event\NotificationEventInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: "notification_event_setting")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class NotificationEventSetting
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'notification_event_setting';

    #[ORM\Id]
    #[ORM\Column(name: "event_name", type: "string", length: 250)]
    private string $eventName;

    #[ORM\Column(type: "boolean")]
    private bool $enabled;

    #[ORM\OneToMany(
        targetEntity: NotificationTemplateSetting::class,
        mappedBy: 'event',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $templates;

    private function __construct()
    {
        $this->templates = new ArrayCollection();
    }

    public static function create(NotificationEventInterface $event, bool $enabled = false): self
    {
        $self =  new self();
        $self->eventName = $event->getName();
        $self->enabled = $enabled;

        return $self;
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

    public function getEventName(): string
    {
        return $this->eventName;
    }
}
