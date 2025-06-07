<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Company\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyReaderRepository extends ServiceEntityRepository implements CompanyReaderInterface
{
    private QueryBuilder $qb;

    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Company::class);
    }

    public function getCompanyByUUID(string $uuid): ?Company
    {
        return $this->findOneBy([Company::COLUMN_UUID => $uuid]);
    }

    public function getCompaniesByUUID(array $selectedUUID): Collection
    {
        if (empty($selectedUUID)) {
            return new ArrayCollection();
        }

        $companies = $this->getEntityManager()
            ->createQuery('SELECT c FROM ' . Company::class . ' c WHERE c.' . Company::COLUMN_UUID . ' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID)
            ->getResult();


        if (count($companies) !== count($selectedUUID)) {
            $missingUuids = array_diff($selectedUUID, array_map(fn($company) => $company->getUUID(), $companies));
            throw new NotFindByUUIDException(sprintf(
                '%s : %s',
                $this->translator->trans('company.uuid.notFound', [], 'companies'),
                implode(', ', $missingUuids)
            ));
        }

        return new ArrayCollection($companies);
    }

    public function getCompanyByFullName(string $fullName, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS . '.' . Company::COLUMN_FULL_NAME . ' = :name')
            ->setParameter('name', $fullName);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS . '.' . Company::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByNIP(string $nip, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS . '.' . Company::COLUMN_NIP . ' = :nip')
            ->setParameter('nip', $nip);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS . '.' . Company::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByREGON(string $regon, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS . '.' . Company::COLUMN_REGON . ' = :regon')
            ->setParameter('regon', $regon);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS . '.' . Company::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByShortName(string $shortName, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS . '.' . Company::COLUMN_SHORT_NAME . ' = :name')
            ->setParameter('name', $shortName);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS . '.' . Company::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isCompanyExistsWithFullName(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getCompanyByFullName($name, $uuid));
    }

    public function isCompanyExistsWithNIP(string $nip, ?string $uuid = null): bool
    {
        return !is_null($this->getCompanyByNIP($nip, $uuid));
    }

    public function isCompanyExistsWithREGON(string $regon, ?string $uuid = null): bool
    {
        return !is_null($this->getCompanyByREGON($regon, $uuid));
    }

    public function isCompanyExistsWithUUID(string $uuid): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS . '.' . Company::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function isCompanyExists(string $nip, string $regon, ?string $companyUUID = null): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(sprintf('%s.%s = :nip OR %s.%s = :regon', Company::ALIAS, Company::COLUMN_NIP, Company::ALIAS, Company::COLUMN_REGON))
            ->setParameter('nip', $nip)
            ->setParameter('regon', $regon);

        if ($companyUUID !== null) {
            $qb->andWhere(sprintf('%s.uuid != :uuid', Company::ALIAS))
                ->setParameter('uuid', $companyUUID);
        }

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
