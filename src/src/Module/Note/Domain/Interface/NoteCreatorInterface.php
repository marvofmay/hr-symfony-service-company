<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

interface NoteCreatorInterface
{
    public function create(string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW, ?Employee $employee = null): void;
}