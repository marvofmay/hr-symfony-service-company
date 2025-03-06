<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\User\Writer;

use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Interface\User\UserWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserWriterRepository extends ServiceEntityRepository implements UserWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, User::class);
    }

    public function deleteUserInDB(?User $user, string $type = User::SOFT_DELETED_AT): void
    {
        if (null === $user) {
            return;
        }

        if ($type === User::HARD_DELETED_AT) {
            $this->getEntityManager()->getRepository(User::class)->createQueryBuilder('user')
                ->delete()
                ->where('user.uuid = :uuid')
                ->setParameter('uuid', $user->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
        }
    }
}
