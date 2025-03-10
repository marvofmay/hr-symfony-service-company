<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Import\Reader;

use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

class ImportReaderRepository extends ServiceEntityRepository implements ImportReaderInterface
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, Import::class);
    }

    public function getImportByFile(File $file): Import|null
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('i')
            ->from(Import::class, 'i')
            ->where('i.' . Import::RELATION_FILE . ' = :file')
            ->setParameter('file', $file);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getImportByUuid(string $uuid): Import|null
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('i')
            ->from(Import::class, 'i')
            ->where('i.' . Import::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return $qb->getQuery()->getOneOrNullResult();
    }
}