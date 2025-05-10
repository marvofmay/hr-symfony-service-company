<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

class NotesQueryDTO extends QueryDTOAbstract
{
    public ?string $title = null;
    public ?string $content = null;
    public ?NotePriorityEnum $priority;
}
