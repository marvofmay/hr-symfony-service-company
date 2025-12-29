<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Position\Reader;

use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Enum\Position\PositionEntityFieldEnum;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class PositionReaderRepository extends ServiceEntityRepository implements PositionReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    public function getPositionByUUID(string $uuid): ?Position
    {
        return $this->findOneBy([PositionEntityFieldEnum::UUID->value => $uuid]);
    }

    public function getPositionsByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Position::ALIAS)
            ->from(Position::class, Position::ALIAS)
            ->where(Position::ALIAS.'.'.PositionEntityFieldEnum::UUID->value.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $positions = $qb->getQuery()->getResult();

        return new ArrayCollection($positions);
    }

    public function getPositionByName(string $name, ?string $uuid = null): ?Position
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from(Position::class, 'p')
            ->where('p.'.PositionEntityFieldEnum::NAME->value.' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('p.'.PositionEntityFieldEnum::UUID->value.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isPositionNameAlreadyExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getPositionByName($name, $uuid));
    }

    public function isPositionWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([PositionEntityFieldEnum::UUID->value => $uuid]);
    }

    public function getDeletedPositionByUUID(string $uuid): ?Position
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            return $this->createQueryBuilder(Position::ALIAS)
                ->where(Position::ALIAS.'.'.PositionEntityFieldEnum::UUID->value.' = :uuid')
                ->andWhere(Position::ALIAS.'.'.TimeStampableEntityFieldEnum::DELETED_AT->value.' IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getPositionsByNames(array $names): Collection
    {
        if (!$names) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Position::ALIAS)
            ->from(Position::class, Position::ALIAS)
            ->where(Position::ALIAS.'.'.PositionEntityFieldEnum::NAME->value.' IN (:names)')
            ->setParameter('names', $names);

        $positions = $qb->getQuery()->getResult();

        return new ArrayCollection($positions);
    }

    public function getSelectOptionsByDepartment(?string $departmentUUID): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT p.uuid AS uuid', 'p.name AS name')
            ->leftJoin(
                'p.positionDepartments',
                'pd',
                'WITH',
                'pd.deletedAt IS NULL'
            )
            ->where('p.active = true')
            ->andWhere('p.deletedAt IS NULL')
            ->orderBy('p.name', 'ASC');

        if ($departmentUUID !== null) {
            $qb
                ->andWhere(
                    'pd.id IS NULL OR pd.department = :departmentUUID'
                )
                ->setParameter('departmentUUID', $departmentUUID);
        }

        return $qb->getQuery()->getArrayResult();
    }
}
