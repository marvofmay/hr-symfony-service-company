<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\AuthEvent\Writer;

use App\Module\System\Domain\Entity\AuthEvent;
use App\Module\System\Domain\Interface\AuthEvent\AuthEventWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuthEventWriterRepository extends ServiceEntityRepository implements AuthEventWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthEvent::class);
    }

    public function saveAuthEventInDB(AuthEvent $authEvent): void
    {
        $this->getEntityManager()->persist($authEvent);
        $this->getEntityManager()->flush();
    }
}
