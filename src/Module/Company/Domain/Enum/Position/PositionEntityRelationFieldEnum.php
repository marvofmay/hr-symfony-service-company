<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Position;

enum PositionEntityRelationFieldEnum: string
{
    case EMPLOYEES = 'employees';
    case POSITION_DEPARTMENTS = 'positionDepartments';
}
