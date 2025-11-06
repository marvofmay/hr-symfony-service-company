<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use Doctrine\Common\Collections\Collection;

interface IndustryMultipleDeleterInterface
{
    public function multipleDelete(Collection $roles): void;
}