<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Email\Writer;

use App\Module\System\Domain\Entity\Email;
use App\Module\System\Domain\Interface\Email\EmailWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmailWriterRepository extends ServiceEntityRepository implements EmailWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    public function saveEmailInDB(Email $email): void
    {
        $this->getEntityManager()->persist($email);
        $this->getEntityManager()->flush();
    }
}
