<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Reader;

use App\Common\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndustryReaderRepository extends ServiceEntityRepository implements IndustryReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Industry::class);
    }

    public function getIndustryByUUID(string $uuid): ?Industry
    {
        $industry = $this->getEntityManager()
            ->createQuery('SELECT r FROM App\Module\Company\Domain\Entity\Industry r WHERE r.' . Industry::COLUMN_UUID. ' = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$industry) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('industry.uuid.notFound', [], 'roles'), $uuid));
        }

        return $industry;
    }

    public function getIndustryByName(string $name, ?string $uuid = null): ?Industry
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('i')
            ->from('App\Module\Company\Domain\Entity\Industry', 'i')
            ->where('i.' . Industry::COLUMN_NAME . ' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('i.' . Industry::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isIndustryExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getIndustryByName($name, $uuid));
    }

    public function isIndustryWithUUIDExists(string $uuid): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('i')
            ->from('App\Module\Company\Domain\Entity\Industry', 'i')
            ->where('i.' . Industry::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
