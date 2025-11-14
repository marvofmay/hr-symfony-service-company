<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

interface NoteUpdaterInterface
{
    public function update(Note $note, string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW): void;
}