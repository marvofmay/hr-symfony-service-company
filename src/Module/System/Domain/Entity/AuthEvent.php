<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\User;
use App\Module\System\Domain\Enum\Auth\AuthEventTypeEnum;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'auth_event')]
#[ORM\Index(name: 'auth_event_user', columns: ['user_uuid'])]
#[ORM\Index(name: 'auth_event_type', columns: ['event_type'])]
#[ORM\Index(name: 'auth_event_created_at', columns: ['created_at'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class AuthEvent
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private UserInterface $user;

    #[ORM\Column(type: 'string', length: 50)]
    private string $eventType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $ip;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $userAgent;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $tokenUUID;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $meta;

    private function __construct(
        UserInterface $user,
        AuthEventTypeEnum $type,
        ?string $ip,
        ?string $userAgent,
        ?TokenUUID $tokenUUID,
        ?array $meta = null,
    ) {
        $this->uuid = Uuid::uuid4();
        $this->user = $user;
        $this->eventType = $type->value;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->tokenUUID = $tokenUUID?->toString();
        $this->meta = $meta;
    }

    public static function create(
        UserInterface $user,
        AuthEventTypeEnum $type,
        ?string $ip = null,
        ?string $userAgent = null,
        ?TokenUUID $tokenUUID = null,
        ?array $meta = null
    ): self {
        return new self(
            $user,
            $type,
            $ip,
            $userAgent,
            $tokenUUID,
            $meta
        );
    }

    public function getUUID(): string
    {
        return $this->uuid->toString();
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getEventType(): AuthEventTypeEnum
    {
        return AuthEventTypeEnum::from($this->eventType);
    }

    public function getIP(): ?string
    {
        return $this->ip;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getTokenUUID(): ?string
    {
        return $this->tokenUUID;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }
}