<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'event_log')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class EventLog
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid {
        get {
            return $this->uuid;
        }
    }

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $event {
        get {
            return $this->event;
        }
    }

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $entity {
        get {
            return $this->entity;
        }
    }

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $data = null {
        get {
            return $this->data;
        }
    }

    public function __construct(string $event, string $entity, ?string $data = null)
    {
        $this->event = $event;
        $this->entity = $entity;
        $this->data = $data;
    }
}