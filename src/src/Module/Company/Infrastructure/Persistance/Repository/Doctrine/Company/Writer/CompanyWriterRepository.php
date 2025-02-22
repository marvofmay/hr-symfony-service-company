<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Company\Writer;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompanyWriterRepository extends ServiceEntityRepository implements CompanyWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function saveCompanyInDB(Company $company): void
    {
        $this->getEntityManager()->persist($company);
        $this->getEntityManager()->flush();
    }

    public function updateCompanyInDB(Company $company): void
    {
        $this->getEntityManager()->flush();
    }

    public function saveCompaniesInDB(array $companies): void
    {
        foreach ($companies as $company) {
            $this->getEntityManager()->persist($company);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleCompaniesInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE App\Module\Company\Domain\Entity\Company c SET c.deletedAt = :deletedAt WHERE c.uuid IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
