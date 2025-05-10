<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Company\Writer;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
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

    public function saveCompaniesInDB(Collection $companies): void
    {
        foreach ($companies as $company) {
            $this->getEntityManager()->persist($company);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteCompanyInDB(Company $company): void {
        $this->getEntityManager()->remove($company);
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleCompaniesInDB(Collection $companies): void
    {
        if (empty($companies)) {
            return;
        }

        foreach ($companies as $company) {
            $this->getEntityManager()->remove($company);
        }

        $this->getEntityManager()->flush();
    }    
}
