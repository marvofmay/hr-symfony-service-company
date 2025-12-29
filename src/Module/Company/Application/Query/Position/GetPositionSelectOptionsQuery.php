<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Position;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Common\Domain\Interface\QueryInterface;

final class GetPositionSelectOptionsQuery implements QueryInterface
{
    public ?string $departmentUUID = null;

    public function __construct(QueryDTOInterface $queryDTO)
    {
        $this->departmentUUID = $queryDTO->departmentUUID;
    }
}
