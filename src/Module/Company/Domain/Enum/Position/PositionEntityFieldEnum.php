<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Position;

enum PositionEntityFieldEnum: string
{
    case UUID = 'uuid';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case ACTIVE = 'active';
}
