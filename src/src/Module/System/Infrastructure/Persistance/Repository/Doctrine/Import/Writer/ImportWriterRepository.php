<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Import\Writer;

use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Interface\Import\ImportWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ImportWriterRepository extends ServiceEntityRepository implements ImportWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
    }

    public function saveImportInDB(Import $import): void
    {
        $this->getEntityManager()->persist($import);
        $this->getEntityManager()->flush();
    }
}
