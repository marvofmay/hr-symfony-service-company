<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\ContractType;

enum ContractTypeEntityFieldEnum: string
{
    case UUID = 'uuid';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case ACTIVE = 'active';
}
