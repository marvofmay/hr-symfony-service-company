<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Industry;

enum IndustryEntityFieldEnum: string
{
    case UUID = 'uuid';
    case NAME = 'name';
    case DESCRIPTION = 'description';
}
