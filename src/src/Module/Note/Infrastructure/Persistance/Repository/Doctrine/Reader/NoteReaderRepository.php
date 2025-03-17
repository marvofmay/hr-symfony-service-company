<?php

declare(strict_types=1);

namespace App\Module\Note\Infrastructure\Persistance\Repository\Doctrine\Reader;

use App\Module\Note\Domain\Entity\Note;
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
        return $this->findOneBy([Note::COLUMN_UUID => $uuid]);
    }

    public function isNoteWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([Note::COLUMN_UUID => $uuid]);
    }
}
