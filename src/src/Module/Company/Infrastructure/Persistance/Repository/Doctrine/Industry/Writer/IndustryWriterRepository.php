<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Writer;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IndustryWriterRepository extends ServiceEntityRepository implements IndustryWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Industry::class);
    }

    public function saveIndustryInDB(Industry $industry): void
    {
        $this->getEntityManager()->persist($industry);
        $this->getEntityManager()->flush();
    }

    public function updateIndustryInDB(Industry $industry): void
    {
        $this->getEntityManager()->flush();
    }

    public function saveIndustriesInDB(array $industries): void
    {
        foreach ($industries as $industry) {
            $this->getEntityManager()->persist($industry);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleIndustriesInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE ' . Industry::class . ' i SET i.' . Industry::COLUMN_DELETED_AT . ' = :deletedAt WHERE i.' . Industry::COLUMN_UUID . ' IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
