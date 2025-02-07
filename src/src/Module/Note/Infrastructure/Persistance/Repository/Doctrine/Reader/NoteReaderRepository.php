<?php

declare(strict_types = 1);

namespace App\Module\Note\Infrastructure\Persistance\Repository\Doctrine\Reader;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Exception\NotFindByUUIDException;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class NoteReaderRepository extends ServiceEntityRepository implements NoteReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Note::class);
    }
    public function getNoteByUUID(string $uuid): ?Note
    {
        $role = $this->getEntityManager()
            ->createQuery('SELECT n FROM App\Module\Note\Domain\Entity\Note n WHERE n.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$role) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('note.uuid.notFound'), $uuid));
        }

        return $role;
    }
}