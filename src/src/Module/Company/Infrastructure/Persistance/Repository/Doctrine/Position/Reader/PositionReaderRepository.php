<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Position\Reader;

use App\Common\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class PositionReaderRepository extends ServiceEntityRepository implements PositionReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Position::class);
    }

    public function getPositionByUUID(string $uuid): ?Position
    {
        $position = $this->getEntityManager()
            ->createQuery('SELECT p FROM App\Module\Company\Domain\Entity\Position p WHERE p.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$position) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('position.uuid.notFound', [], 'positions'), $uuid));
        }

        return $position;
    }

    public function getPositionByName(string $name, ?string $uuid = null): ?Position
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from('App\Module\Company\Domain\Entity\Position', 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('p.uuid != :uuid')
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
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from('App\Module\Company\Domain\Entity\Position', 'p')
            ->where('p.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        return null === $qb->getQuery()->getOneOrNullResult();
    }
}
