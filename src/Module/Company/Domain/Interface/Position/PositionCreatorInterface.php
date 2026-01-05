<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

interface PositionCreatorInterface
{
    public function create(string $name, ?string $description = null, bool $active = false, array $departmentsUUIDs = []): void;
}
