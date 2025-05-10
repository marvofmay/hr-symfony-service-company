<?php

declare(strict_types=1);

namespace App\Module\Note\Infrastructure\Persistance\Repository\Doctrine\Writer;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Interface\NoteWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteWriterRepository extends ServiceEntityRepository implements NoteWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function saveNoteInDB(Note $note): void
    {
        $this->getEntityManager()->persist($note);
        $this->getEntityManager()->flush();
    }

    public function deleteNoteInDB(Note $note): void
    {
        $this->getEntityManager()->remove($note);
        $this->getEntityManager()->flush();
    }
}
