<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position\Import;

interface PositionsImporterInterface
{
    public function save(array $positionNameMap, array $groupPositions, array $existingPositions, array $existingDepartments): void;
}