<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum;

enum TimeStampableEntityFieldEnum: string
{
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';
    case DELETED_AT = 'deletedAt';
}
