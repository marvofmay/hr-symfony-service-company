<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Note\Domain\Entity\Note;
use Doctrine\Common\Collections\Collection;

interface NoteWriterInterface
{
    public function save(Note $note): void;

    public function delete(Note $note): void;
    public function deleteMultipleNotes(Collection $notes): void;
}
