<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use App\Module\Company\Domain\Entity\Industry;
use Doctrine\Common\Collections\Collection;

interface IndustryReaderInterface
{
    public function getIndustryByUUID(string $uuid): ?Industry;

    public function getIndustriesByUUID(array $selectedUUID): Collection;

    public function getIndustryByName(string $name, ?string $uuid): ?Industry;

    public function isIndustryNameAlreadyExists(string $name, ?string $uuid = null): bool;

    public function isIndustryExistsWithUUID(string $uuid): bool;
}
