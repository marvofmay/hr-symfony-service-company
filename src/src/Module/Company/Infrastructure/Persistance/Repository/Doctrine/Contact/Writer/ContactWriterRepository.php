<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Contact\Writer;

use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class ContactWriterRepository extends ServiceEntityRepository implements ContactWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function deleteContactsInDB(Collection $contacts, string $type = Contact::SOFT_DELETED_AT): void {
        foreach ($contacts as $contact) {
            if ($type === Contact::HARD_DELETED_AT) {
                $this->getEntityManager()->getRepository(Contact::class)->createQueryBuilder('contact')
                    ->delete()
                    ->where('contact.uuid = :uuid')
                    ->setParameter('uuid', $contact->getUUID())
                    ->getQuery()
                    ->execute();
            } else {
                $this->getEntityManager()->remove($contact);
                $this->getEntityManager()->flush();
            }
        }
    }
}
