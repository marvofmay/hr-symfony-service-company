<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use App\Module\Company\Domain\Entity\Industry;

interface IndustryRestorerInterface
{
    public function restore(Industry $industry): void;
}
