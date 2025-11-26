<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Import\Reader;

use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ImportReaderRepository extends ServiceEntityRepository implements ImportReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
    }

    public function getImportByFile(File $file): ?Import
    {
        return $this->findOneBy([Import::RELATION_FILE => $file]);
    }

    public function getImportByUuid(string $uuid): ?Import
    {
        return $this->findOneBy([Import::COLUMN_UUID => $uuid]);
    }
}
