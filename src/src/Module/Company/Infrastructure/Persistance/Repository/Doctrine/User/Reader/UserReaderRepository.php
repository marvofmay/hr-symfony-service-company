<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\User\Reader;

use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Interface\User\UserReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final class UserReaderRepository extends ServiceEntityRepository implements UserReaderInterface
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, User::class);
    }

    public function getUserByUUID(string $userUUID): ?User
    {
        return $this->findOneBy(['uuid' => $userUUID]);
    }

    public function getUsersEmailsByUUIDs(array $userUUIDs): array
    {
        if (empty($userUUIDs)) {
            return [];
        }

        $qb = $this->createQueryBuilder('u')
            ->select('u.email')
            ->where('u.uuid IN (:uuids)')
            ->setParameter('uuids', $userUUIDs);

        $results = $qb->getQuery()->getResult();

        $emails = array_column($results, 'email');

        return array_values(array_unique($emails));
    }
}
