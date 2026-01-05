<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use App\Module\Company\Domain\Entity\Industry;

interface IndustryUpdaterInterface
{
    public function update(Industry $industry, string $name, ?string $description = null): void;
}
