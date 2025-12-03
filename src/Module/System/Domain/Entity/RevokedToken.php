<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Module\Company\Domain\Entity\User;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'revoked_token')]
#[ORM\Index(name: 'user_uuid', columns: ['user_uuid'])]
#[ORM\Index(name: 'token_uuid', columns: ['token_uuid'])]
#[ORM\Index(name: 'revoked_at', columns: ['revoked_at'])]
class RevokedToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $tokenUUID;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private UserInterface $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $revokedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expiresAt;

    private function __construct(UserInterface $user, TokenUUID $tokenUUID, ?\DateTime $expiresAt = null)
    {
        $this->user = $user;
        $this->tokenUUID = $tokenUUID->toString();
        $this->expiresAt = $expiresAt;
        $this->revokedAt = new \DateTime();
    }

    public static function create(UserInterface $user, TokenUUID $tokenUUID, ?\DateTime $expiresAt = null): self
    {
        return new self($user, $tokenUUID, $expiresAt);
    }

    public function getTokenUUID(): string
    {
        return $this->tokenUUID;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }
}
