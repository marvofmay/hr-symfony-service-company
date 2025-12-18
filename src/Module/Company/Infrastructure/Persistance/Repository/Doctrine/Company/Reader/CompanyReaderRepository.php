<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Company\Reader;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CompanyReaderRepository extends ServiceEntityRepository implements CompanyReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function getCompanyByUUID(string $uuid): Company
    {
        return $this->findOneBy([Company::COLUMN_UUID => $uuid]);
    }

    public function getCompaniesByUUID(array $selectedUUIDs): Collection
    {
        if (empty($selectedUUIDs)) {
            return new ArrayCollection();
        }

        $companies = $this->getEntityManager()
            ->createQuery('SELECT c FROM '.Company::class.' c WHERE c.'.Company::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUIDs)
            ->getResult();

        return new ArrayCollection($companies);
    }

    public function getCompaniesByNIP(array $selectedNIP): Collection
    {
        if (empty($selectedNIP)) {
            return new ArrayCollection();
        }

        $companies = $this->getEntityManager()
            ->createQuery('SELECT c FROM '.Company::class.' c WHERE c.'.Company::COLUMN_NIP.' IN (:nips)')
            ->setParameter('nips', $selectedNIP)
            ->getResult();

        return new ArrayCollection($companies);
    }

    public function getCompanyByFullName(string $fullName, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS.'.'.Company::COLUMN_FULL_NAME.' = :name')
            ->setParameter('name', $fullName);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS.'.'.Company::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByInternalCode(string $internalCode, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS.'.'.Company::COLUMN_INTERNAL_CODE.' = :internalCode')
            ->setParameter('internalCode', $internalCode);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS.'.'.Company::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByNIP(string $nip, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS.'.'.Company::COLUMN_NIP.' = :nip')
            ->setParameter('nip', $nip);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS.'.'.Company::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByREGON(string $regon, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS.'.'.Company::COLUMN_REGON.' = :regon')
            ->setParameter('regon', $regon);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS.'.'.Company::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCompanyByShortName(string $shortName, ?string $uuid = null): ?Company
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(Company::ALIAS.'.'.Company::COLUMN_SHORT_NAME.' = :name')
            ->setParameter('name', $shortName);

        if (null !== $uuid) {
            $qb->andWhere(Company::ALIAS.'.'.Company::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isCompanyExistsWithFullName(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getCompanyByFullName($name, $uuid));
    }

    public function isCompanyExistsWithInternalCode(string $internalCode, ?string $uuid = null): bool
    {
        return !is_null($this->getCompanyByInternalCode($internalCode, $uuid));
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
            ->where(Company::ALIAS.'.'.Company::COLUMN_UUID.' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function isCompanyExists(string $nip, string $regon, ?string $companyUUID = null): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(Company::ALIAS)
            ->from(Company::class, Company::ALIAS)
            ->where(
                sprintf(
                    '%s.%s = :nip OR %s.%s = :regon',
                    Company::ALIAS,
                    Company::COLUMN_NIP,
                    Company::ALIAS,
                    Company::COLUMN_REGON,
                )
            )
            ->setParameter('nip', $nip)
            ->setParameter('regon', $regon);

        if (null !== $companyUUID) {
            $qb->andWhere(sprintf('%s.uuid != :uuid', Company::ALIAS))
                ->setParameter('uuid', $companyUUID);
        }

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function getDeletedCompanyByUUID(string $uuid): ?Company
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $deletedCompany = $this->createQueryBuilder(Company::ALIAS)
                ->where(Company::ALIAS.'.'.Company::COLUMN_UUID.' = :uuid')
                ->andWhere(Company::ALIAS.'.'.Company::COLUMN_DELETED_AT.' IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();

            return $deletedCompany;
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getDeletedAddressByCompanyUUID(string $uuid): ?Address
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $deletedAddress = $qb->select('a')
                ->from(Address::class, 'a')
                ->join('a.company', 'c')
                ->where('c.uuid = :uuid')
                ->andWhere('a.deletedAt IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();

            return $deletedAddress;
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getDeletedContactsByCompanyUUID(string $uuid): Collection
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $deletedContacts = $qb->select(Company::ALIAS)
                ->from(Contact::class, Company::ALIAS)
                ->join(Company::ALIAS.'.company', Contact::ALIAS)
                ->where(Contact::ALIAS.'.'.Company::COLUMN_UUID.'= :uuid')
                ->andWhere(Company::ALIAS.'.deletedAt IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getResult();

            return new ArrayCollection($deletedContacts);
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getCompaniesNIPByEmails(array $emails): Collection
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(Company::ALIAS.'.'.Company::COLUMN_NIP, Contact::ALIAS.'.'.Contact::COLUMN_DATA.' AS email')
            ->from(Company::class, Company::ALIAS)
            ->join(Company::ALIAS.'.'.Company::RELATION_CONTACTS, Contact::ALIAS)
            ->where(Contact::ALIAS.'.'.Contact::COLUMN_DATA.' IN (:emails)')
            ->setParameter('emails', $emails);

        $results = $qb->getQuery()->getArrayResult();

        return new ArrayCollection($results);
    }

    public function getAllDescendantUUIDs(string $parentUuid): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
WITH RECURSIVE company_tree AS (
    SELECT uuid, company_uuid, full_name
    FROM company
    WHERE uuid = :uuid

    UNION ALL

    SELECT c.uuid, c.company_uuid, c.full_name
    FROM company c
    INNER JOIN company_tree ct ON c.company_uuid = ct.uuid
)
SELECT uuid
FROM company_tree
WHERE uuid != :uuid
ORDER BY full_name;
SQL;

        $result = $conn->executeQuery($sql, ['uuid' => $parentUuid]);

        return array_column($result->fetchAllAssociative(), 'uuid');
    }

    public function getAvailableParentCompanyOptions(?string $companyUUID = null): array
    {
        $conn = $this->getEntityManager()->getConnection();

        if ($companyUUID !== null) {
            $sql = <<<SQL
WITH RECURSIVE company_tree AS (
    SELECT uuid
    FROM company
    WHERE uuid = :companyUuid

    UNION ALL

    SELECT c.uuid
    FROM company c
    INNER JOIN company_tree ct ON c.company_uuid = ct.uuid
)
SELECT c.uuid, c.full_name AS name
FROM company c
WHERE c.uuid NOT IN (SELECT uuid FROM company_tree)
ORDER BY c.full_name;
SQL;

            $params = [
                'companyUuid' => $companyUUID,
            ];
        } else {
            $sql = <<<SQL
SELECT c.uuid, c.full_name AS name
FROM company c
ORDER BY c.full_name;
SQL;

            $params = [];
        }

        return $conn->executeQuery($sql, $params)->fetchAllAssociative();
    }
}
