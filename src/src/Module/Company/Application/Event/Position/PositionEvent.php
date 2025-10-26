<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Position;

use App\Module\Company\Application\Event\Event;
use App\Module\Company\Domain\Entity\Position;

class PositionEvent extends Event
{
    public function getEntityClass(): string
    {
        return Position::class;
    }
}
