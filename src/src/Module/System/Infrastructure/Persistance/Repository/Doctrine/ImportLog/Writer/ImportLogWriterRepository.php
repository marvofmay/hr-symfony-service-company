<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\ImportLog\Writer;

use App\Module\System\Domain\Entity\ImportLog;
use App\Module\System\Domain\Interface\ImportLog\ImportLogWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class ImportLogWriterRepository extends ServiceEntityRepository implements ImportLogWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportLog::class);
    }

    public function saveImportLogsInDB(Collection $importLogs): void
    {
        foreach ($importLogs as $importLog) {
            $this->getEntityManager()->persist($importLog);
        }

        $this->getEntityManager()->flush();
    }
}
