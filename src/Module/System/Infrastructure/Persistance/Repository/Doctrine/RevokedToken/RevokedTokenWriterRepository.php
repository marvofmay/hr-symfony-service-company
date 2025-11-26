<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\RevokedToken;

use App\Module\System\Domain\Entity\RevokedToken;
use App\Module\System\Domain\Interface\RevokedToken\RevokedTokenWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RevokedTokenWriterRepository extends ServiceEntityRepository implements RevokedTokenWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevokedToken::class);
    }

    public function save(RevokedToken $revokedToken): void
    {
        $this->getEntityManager()->persist($revokedToken);
        $this->getEntityManager()->flush();
    }
}
