<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Event;

use App\Module\Note\Domain\Entity\Note;
use App\Module\System\Application\Event\Event;

class NoteEvent extends Event
{
    public function getEntityClass(): string
    {
        return Note::class;
    }
}
