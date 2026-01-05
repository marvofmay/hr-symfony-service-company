<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\AuthEvent;

use App\Module\System\Domain\Entity\RevokedToken;
use App\Module\System\Domain\Interface\RevokedToken\RevokedTokenReaderInterface;
use App\Module\System\Domain\Interface\RevokedToken\RevokedTokenWriterInterface;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class BlacklistTokenService
{
    public function __construct(
        private RevokedTokenReaderInterface $revokedTokenReaderRepository,
        private RevokedTokenWriterInterface $revokedTokenWriterRepository,
    ) {

    }
    public function revoke(UserInterface $user, TokenUUID $tokenUUID, ?\DateTimeInterface $expiresAt = null): void
    {
        $revokedToken = RevokedToken::create($user, $tokenUUID, $expiresAt);
        $this->revokedTokenWriterRepository->save($revokedToken);
    }

    public function isRevoked(TokenUUID $tokenUUID): bool
    {
        return $this->revokedTokenReaderRepository->isRevoked($tokenUUID);
    }
}
