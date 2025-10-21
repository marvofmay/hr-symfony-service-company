<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Writer;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class IndustryWriterRepository extends ServiceEntityRepository implements IndustryWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Industry::class);
    }

    public function saveIndustryInDB(Industry $industry): void
    {
        $this->getEntityManager()->persist($industry);
        $this->getEntityManager()->flush();
    }

    public function saveIndustriesInDB(Collection $industries): void
    {
        foreach ($industries as $industry) {
            $this->getEntityManager()->persist($industry);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteIndustryInDB(Industry $industry): void
    {
        $this->getEntityManager()->remove($industry);
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleIndustriesInDB(Collection $industries): void
    {
        foreach ($industries as $industry) {
            $this->getEntityManager()->remove($industry);
        }
        $this->getEntityManager()->flush();
    }
}
