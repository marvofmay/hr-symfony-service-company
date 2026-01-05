<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;

interface PositionDepartmentUpdaterInterface
{
    public function updateDepartments(Position $position, array $departmentsUUIDs = []): void;
}
