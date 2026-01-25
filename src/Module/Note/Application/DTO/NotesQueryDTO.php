<?php

declare(strict_types=1);

namespace App\Module\Note\Application\DTO;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class NotesQueryDTO extends QueryDTOAbstract
{
    public ?string $title = null;
    public ?string $content = null;
    public ?string $priority = null;
    public ?string $userUUID = null;
}
