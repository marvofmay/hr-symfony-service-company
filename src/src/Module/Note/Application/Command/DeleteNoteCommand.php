<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Command;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteNoteCommand implements CommandInterface
{
    public const string NOTE_UUID = 'noteUUID';

    public function __construct(public string $noteUUID)
    {
    }
}
