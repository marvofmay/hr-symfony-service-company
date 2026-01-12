<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Command;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteMultipleNotesCommand implements CommandInterface
{
    public const string NOTES_UUIDS = 'notesUUIDs';
    public function __construct(public array $notesUUIDs)
    {
    }
}
