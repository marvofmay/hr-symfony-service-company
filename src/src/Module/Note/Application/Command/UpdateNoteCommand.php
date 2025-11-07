<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Command;

use App\Common\Domain\Interface\CommandInterface;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

final readonly class UpdateNoteCommand implements CommandInterface
{
    public const string UUID = 'noteUUID';
    public const string TITLE   = 'title';
    public const string CONTENT  = 'content';
    public const string PRIORITY = 'priority';
    
    public function __construct(
        public string $noteUUID,
        public string $title,
        public ?string $content,
        public NotePriorityEnum $priority,
    ) {
    }
}
