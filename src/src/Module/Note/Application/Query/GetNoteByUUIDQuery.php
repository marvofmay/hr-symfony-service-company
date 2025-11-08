<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Query;

use App\Common\Domain\Interface\QueryInterface;

final readonly class GetNoteByUUIDQuery implements QueryInterface
{
    public const string NOTE_UUID = 'noteUUID';

    public function __construct(public string $noteUUID)
    {
    }
}
