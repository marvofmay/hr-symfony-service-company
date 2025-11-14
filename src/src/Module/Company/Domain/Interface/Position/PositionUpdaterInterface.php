<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;

interface PositionUpdaterInterface
{
    public function update(Position $position, string $name, ?string $description = null, bool $active = false, array $departmentsUUIDs = []): void;
}