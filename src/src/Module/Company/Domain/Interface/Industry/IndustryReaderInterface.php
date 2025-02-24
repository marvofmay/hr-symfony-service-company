<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use App\Module\Company\Domain\Entity\Industry;

interface IndustryReaderInterface
{
    public function getIndustryByUUID(string $uuid): ?Industry;

    public function getIndustryByName(string $name, ?string $uuid): ?Industry;
}
