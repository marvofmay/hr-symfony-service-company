<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;
use Doctrine\Common\Collections\Collection;

interface PositionReaderInterface
{
    public function getPositionByUUID(string $uuid): ?Position;

    public function getPositionsByUUID(array $selectedUUID): Collection;

    public function getPositionByName(string $name, ?string $uuid): ?Position;

    public function isPositionNameAlreadyExists(string $name, ?string $uuid = null): bool;

    public function isPositionWithUUIDExists(string $uuid): bool;

    public function getDeletedPositionByUUID(string $uuid): ?Position;

    public function getPositionsByNames(array $names): Collection;
}
