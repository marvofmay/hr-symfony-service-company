<?php

declare(strict_types=1);

namespace App\Module\Note\Infrastructure\Persistance\Repository\Doctrine\Reader;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
use App\Module\Note\Domain\Enum\NoteEntityRelationFieldEnum;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Note>
 */
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

    public function getNoteByUUIDAndUser(string $uuid, UserInterface $user): ?Note
    {
        return $this->findOneBy([
            NoteEntityFieldEnum::UUID->value         => $uuid,
            NoteEntityRelationFieldEnum::USER->value => $user,
        ]);
    }

    public function getNotesByUUIDsAndUser(array $uuids, UserInterface $user): Collection
    {
        if (empty($uuids)) {
            return new ArrayCollection();
        }

        $qb = $this->createQueryBuilder('n')
            ->where('n.uuid IN (:uuids)')
            ->setParameter('uuids', $uuids)
            ->andWhere('n.user = :user')
            ->setParameter('user', $user);

        $notes = $qb->getQuery()->getResult();

        return new ArrayCollection($notes);
    }

    public function isNoteWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([NoteEntityFieldEnum::UUID->value => $uuid]);
    }

    public function isNoteWithUUIDAndUserExists(string $uuid, UserInterface $user): bool
    {
        return null !== $this->findOneBy([
                NoteEntityFieldEnum::UUID->value         => $uuid,
                NoteEntityRelationFieldEnum::USER->value => $user,
            ]);
    }
}
