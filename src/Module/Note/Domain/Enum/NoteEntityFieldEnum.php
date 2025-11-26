<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Enum;

enum NoteEntityFieldEnum: string
{
    case UUID = 'uuid';
    case TITLE = 'title';
    case CONTENT = 'content';
    case PRIORITY = 'priority';
}
