<?php

namespace App\Module\Note\Application\Command;

use App\Module\Note\Domain\Entity\Note;

readonly class DeleteNoteCommand
{
    public function __construct(private Note $note) {}

    public function getNote(): Note
    {
        return $this->note;
    }
}