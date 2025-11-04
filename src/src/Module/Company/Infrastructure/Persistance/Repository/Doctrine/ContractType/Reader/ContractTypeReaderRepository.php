<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeEntityFieldEnum;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class ContractTypeReaderRepository extends ServiceEntityRepository implements ContractTypeReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContractType::class);
    }

    public function getContractTypeByUUID(string $uuid): ?ContractType
    {
        return $this->findOneBy([ContractType::COLUMN_UUID => $uuid]);
    }

    public function getContractTypesByUUIDs(array $contractTypesUUIDs): Collection
    {
        if (!$contractTypesUUIDs) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(ContractType::ALIAS)
            ->from(ContractType::class, ContractType::ALIAS)
            ->where(ContractType::ALIAS.'.'.ContractType::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $contractTypesUUIDs);

        $contractTypes = $qb->getQuery()->getResult();

        return new ArrayCollection($contractTypes);
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

    public function isContractTypeNameAlreadyExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getContractTypeByName($name, $uuid));
    }

    public function isContractTypeWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid]);
    }

    public function getDeletedContractTypeByUUID(string $uuid): ?ContractType
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            return $this->createQueryBuilder(ContractType::ALIAS)
                ->where(ContractType::ALIAS.'.'.ContractTypeEntityFieldEnum::UUID->value.' = :uuid')
                ->andWhere(ContractType::ALIAS.'.'.TimeStampableEntityFieldEnum::DELETED_AT->value.' IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();
        } finally {
            $filters->enable('soft_delete');
        }
    }
    public function getContractTypesByNames(array $names): Collection
    {
        if (!$names) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(ContractType::ALIAS)
            ->from(ContractType::class, ContractType::ALIAS)
            ->where(ContractType::ALIAS.'.'.ContractTypeEntityFieldEnum::NAME->value.' IN (:names)')
            ->setParameter('names', $names);

        $contractTypes = $qb->getQuery()->getResult();

        return new ArrayCollection($contractTypes);
    }
}
