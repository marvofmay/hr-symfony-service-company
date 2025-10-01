<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Position\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PositionReaderRepository extends ServiceEntityRepository implements PositionReaderInterface
{
    public function __construct(ManagerRegistry $registry, private TranslatorInterface $translator)
    {
        parent::__construct($registry, Position::class);
    }

    public function getPositionByUUID(string $uuid): ?Position
    {
        $position = $this->findOneBy([Position::COLUMN_UUID => $uuid]);
        if (null === $position) {
            throw new \Exception($this->translator->trans('position.uuid.notExists', [':uuid' => $uuid], 'positions'), Response::HTTP_NOT_FOUND);
        }

        return $position;
    }

    public function getPositionsByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Position::ALIAS)
            ->from(Position::class, Position::ALIAS)
            ->where(Position::ALIAS.'.'.Position::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $positions = $qb->getQuery()->getResult();

        //$foundUUIDs = array_map(fn (Position $position) => $position->getUUID(), $positions);
        //$missingUUIDs = array_diff($selectedUUID, $foundUUIDs);
        //
        //if ($missingUUIDs) {
        //    throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('position.uuid.notFound', [], 'positions'), implode(', ', $missingUUIDs)));
        //}

        return new ArrayCollection($positions);
    }

    public function getPositionByName(string $name, ?string $uuid = null): ?Position
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from(Position::class, 'p')
            ->where('p.'.Position::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('p.'.Position::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isPositionExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getPositionByName($name, $uuid));
    }

    public function isPositionWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([Position::COLUMN_UUID => $uuid]);
    }
}
