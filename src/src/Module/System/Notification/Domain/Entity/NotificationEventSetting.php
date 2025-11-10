<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
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
    #[ORM\Column(type: "string", length: 250)]
    private string $notificationEvent;
}