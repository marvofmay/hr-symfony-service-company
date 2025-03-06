<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Address\Writer;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AddressWriterRepository extends ServiceEntityRepository implements AddressWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Address::class);
    }

    public function deleteAddressInDB(?Address $address, string $type = Address::SOFT_DELETED_AT): void
    {
        if (null === $address) {
            return;
        }

        if ($type === Address::HARD_DELETED_AT) {
            $this->getEntityManager()->getRepository(Address::class)->createQueryBuilder('address')
                ->delete()
                ->where('address.uuid = :uuid')
                ->setParameter('uuid', $address->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $this->getEntityManager()->remove($address);
            $this->getEntityManager()->flush();
        }
    }
}
