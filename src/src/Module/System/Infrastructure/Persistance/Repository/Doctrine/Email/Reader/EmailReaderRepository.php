<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Email\Reader;

use App\Module\System\Domain\Entity\Email;
use App\Module\System\Domain\Interface\Email\EmailReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmailReaderRepository extends ServiceEntityRepository implements EmailReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    public function getEmailByUUID(string $emailUUID): Email
    {
        return $this->findOneBy(['uuid' => $emailUUID]);
    }
}
