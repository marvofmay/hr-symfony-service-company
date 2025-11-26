<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use Doctrine\Common\Collections\Collection;

interface PositionMultipleDeleterInterface
{
    public function multipleDelete(Collection $positions): void;
}
