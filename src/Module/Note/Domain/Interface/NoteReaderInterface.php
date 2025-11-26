<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Note\Domain\Entity\Note;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

interface NoteReaderInterface
{
    public function getNoteByUUID(string $uuid): ?Note;
    public function getNotesByUUIDsAndUser(array $uuids, UserInterface $user): Collection;
    public function getNoteByUUIDAndUser(string $uuid, UserInterface $user): ?Note;

    public function isNoteWithUUIDExists(string $uuid): bool;
    public function isNoteWithUUIDAndUserExists(string $uuid, UserInterface $user): bool;
}
