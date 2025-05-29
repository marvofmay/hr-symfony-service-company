<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\ImportLog\Reader;

use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Entity\ImportLog;
use App\Module\System\Domain\Interface\ImportLog\ImportLogReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;

class ImportLogReaderRepository extends ServiceEntityRepository implements ImportLogReaderInterface
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, ImportLog::class);
    }

    public function getImportLogsByImport(Import $import): Collection
    {
        return new ArrayCollection($this->findBy([ImportLog::RELATION_IMPORT => $import]));
    }
}