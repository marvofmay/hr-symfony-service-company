<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Query;

use App\Common\Domain\Interface\QueryInterface;

final class GetNotesPDFQuery implements QueryInterface
{
    public const string NOTES_UUIDS = 'notesUUIDs';

    public function __construct(public array $notesUUIDs = [])
    {
    }
}
