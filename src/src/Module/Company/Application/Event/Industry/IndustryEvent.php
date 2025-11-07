<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\System\Application\Event\Event;

class IndustryEvent extends Event
{
    public function getEntityClass(): string
    {
        return Industry::class;
    }
}
