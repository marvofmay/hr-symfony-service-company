<?php

declare(strict_types=1);

namespace App\Module\Note\Infrastructure\Persistance\Repository\Doctrine\Reader;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
use App\Module\Note\Domain\Enum\NoteEntityRelationFieldEnum;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class NoteReaderRepository extends ServiceEntityRepository implements NoteReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function getNoteByUUID(string $uuid): ?Note
    {
        return $this->findOneBy([NoteEntityFieldEnum::UUID->value => $uuid]);
    }

    public function isNoteWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([NoteEntityFieldEnum::UUID->value => $uuid]);
    }

    public function isNoteWithUUIDAndEmployeeExists(string $uuid, ?Employee $employee): bool
    {
        return null !== $this->findOneBy([
                NoteEntityFieldEnum::UUID->value             => $uuid,
                NoteEntityRelationFieldEnum::EMPLOYEE->value => $employee,
            ]);
    }
}
