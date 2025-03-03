<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Company\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyReaderRepository extends ServiceEntityRepository implements CompanyReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Company::class);
    }

    public function getCompanyByUUID(string $uuid): ?Company
    {
        $position = $this->getEntityManager()
            ->createQuery('SELECT c FROM ' . Company::class . ' c WHERE c.' . Company::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$position) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('company.uuid.notFound', [], 'companies'), $uuid));
        }

        return $position;
    }

    public function getCompanyByFullName(string $fullName, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from(Company::class, 'c')
            ->where('c.' . Company::COLUMN_FULL_NAME . ' = :name')
            ->setParameter('name', $fullName);

        if (null !== $uuid) {
            $qb->andWhere('c.' . Company::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByShortName(string $shortName, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from(Company::class, 'c')
            ->where('c.' . Company::COLUMN_SHORT_NAME . ' = :name')
            ->setParameter('name', $shortName);

        if (null !== $uuid) {
            $qb->andWhere('c.' . Company::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isCompanyExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getCompanyByFullName($name, $uuid));
    }

    public function isCompanyWithUUIDExists(string $uuid): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from(Company::class, 'c')
            ->where('c.' . Company::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
