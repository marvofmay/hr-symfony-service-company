<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\PositionDepartment;

use Doctrine\Common\Collections\Collection;

interface PositionDepartmentReaderInterface
{
    public function getDeletedPositionDepartmentsByPositionUUID(string $positionUUID): Collection;
}
