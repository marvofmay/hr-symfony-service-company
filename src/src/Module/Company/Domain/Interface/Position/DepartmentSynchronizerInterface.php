<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;

interface DepartmentSynchronizerInterface
{
    public function syncDepartments(Position $position, array $payloadInternalCodes, array $existingDepartments): void;
}