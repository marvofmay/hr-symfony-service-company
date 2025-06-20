<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContractTypeReaderRepository extends ServiceEntityRepository implements ContractTypeReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, ContractType::class);
    }

    public function getContractTypeByUUID(string $uuid): ?ContractType
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function getContractTypeByName(string $name, ?string $uuid = null): ?ContractType
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('ct')
            ->from(ContractType::class, 'ct')
            ->where('ct.'.ContractType::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if ($uuid) {
            $qb->andWhere('ct.'.ContractType::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isContractTypeExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getContractTypeByName($name, $uuid));
    }

    public function isContractTypeWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid]);
    }
}
