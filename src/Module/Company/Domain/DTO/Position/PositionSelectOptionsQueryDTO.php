<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use App\Common\Domain\Interface\QueryDTOInterface;

class PositionSelectOptionsQueryDTO implements QueryDTOInterface
{
    public ?string $departmentUUID = null;
}
