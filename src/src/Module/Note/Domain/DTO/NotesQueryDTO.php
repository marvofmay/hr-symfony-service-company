<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class NotesQueryDTO extends QueryDTOAbstract
{
    public ?string $title = null;
    public ?string $content = null;
    public ?string $priority = null;
    public ?string $user = null;
}
