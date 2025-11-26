<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RevokedToken;

use App\Module\System\Domain\Entity\RevokedToken;
use App\Module\System\Domain\Interface\RevokedToken\RevokedTokenReaderInterface;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RevokedTokenReaderRepository extends ServiceEntityRepository implements RevokedTokenReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevokedToken::class);
    }

    public function getByTokenUUID(TokenUUID $tokenUUID): ?RevokedToken
    {
        return $this->findOneBy(['tokenUUID' => $tokenUUID->toString()]);
    }

    public function isRevoked(TokenUUID $tokenUUID): bool
    {
       return null !== $this->getByTokenUUID($tokenUUID);
    }
}
