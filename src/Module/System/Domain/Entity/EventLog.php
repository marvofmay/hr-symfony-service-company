<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'event_log')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class EventLog
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $event;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $entity;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $data;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'eventLogs')]
    #[ORM\JoinColumn(name: 'user_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?UserInterface $user;

    private function __construct()
    {
    }

    public static function create(string $event, string $entity, ?string $data = null, ?UserInterface $user = null): self
    {
        $self = new self();
        $self->uuid = Uuid::uuid4();
        $self->event = $event;
        $self->entity = $entity;
        $self->data = $data;
        $self->user = $user;

        return $self;
    }
}
