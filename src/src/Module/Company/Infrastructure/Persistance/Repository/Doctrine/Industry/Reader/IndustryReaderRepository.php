<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Reader;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityFieldEnum;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class IndustryReaderRepository extends ServiceEntityRepository implements IndustryReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Industry::class);
    }

    public function getIndustryByUUID(string $uuid): ?Industry
    {
        return $this->findOneBy([IndustryEntityFieldEnum::UUID->value => $uuid]);
    }

    public function getIndustriesByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Industry::ALIAS)
            ->from(Industry::class, Industry::ALIAS)
            ->where(Industry::ALIAS.'.'.IndustryEntityFieldEnum::UUID->value.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $industries = $qb->getQuery()->getResult();

        return new ArrayCollection($industries);
    }

    public function getIndustryByName(string $name, ?string $uuid = null): ?Industry
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('i')
            ->from(Industry::class, 'i')
            ->where('i.'.IndustryEntityFieldEnum::NAME->value.' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('i.'.IndustryEntityFieldEnum::UUID->value.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isIndustryNameAlreadyExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getIndustryByName($name, $uuid));
    }

    public function isIndustryExistsWithUUID(string $uuid): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid]);
    }

    public function getDeletedIndustryByUUID(string $uuid): ?Industry
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            return $this->createQueryBuilder(Industry::ALIAS)
                ->where(Industry::ALIAS.'.'.IndustryEntityFieldEnum::UUID->value.' = :uuid')
                ->andWhere(Industry::ALIAS.'.'.TimeStampableEntityFieldEnum::DELETED_AT->value.' IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getIndustriesByNames(array $names): Collection
    {
        if (!$names) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Industry::ALIAS)
            ->from(Industry::class, Industry::ALIAS)
            ->where(Industry::ALIAS.'.'.IndustryEntityFieldEnum::NAME->value.' IN (:names)')
            ->setParameter('names', $names);

        $industries = $qb->getQuery()->getResult();

        return new ArrayCollection($industries);
    }
}
