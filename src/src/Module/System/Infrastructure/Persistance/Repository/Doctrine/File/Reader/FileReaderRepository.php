<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\File\Reader;

use App\Common\Domain\Enum\FileKindEnum;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Interface\File\FileReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FileReaderRepository extends ServiceEntityRepository implements FileReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function getFileByNamePathAndKind(string $fileName, string $filePath, FileKindEnum $enum): ?File
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('f')
            ->from(File::class, 'f')
            ->where('f.'.File::COLUMN_FILE_NAME.' = :name')
            ->andWhere('f.'.File::COLUMN_FILE_PATH.' = :path')
            ->andWhere('f.'.File::COLUMN_KIND.' = :kind')
            ->setParameter('name', $fileName)
            ->setParameter('path', $filePath)
            ->setParameter('kind', $enum->value);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
