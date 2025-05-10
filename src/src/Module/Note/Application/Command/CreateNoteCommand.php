<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Command;

use App\Module\Note\Domain\Enum\NotePriorityEnum;

class CreateNoteCommand
{
    public function __construct(public ?string $title, public ?string $content, public NotePriorityEnum $priority,)
    {
    }
}
