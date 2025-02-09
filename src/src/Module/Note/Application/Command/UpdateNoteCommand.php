<?php

declare(strict_types = 1);

namespace App\Module\Note\Application\Command;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

class UpdateNoteCommand
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $title,
        private readonly ?string $content,
        private readonly NotePriorityEnum $priority,
        private readonly Note $note
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getPriority(): NotePriorityEnum
    {
        return $this->priority;
    }

    public function getNote(): Note
    {
        return $this->note;
    }
}