<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\System\Application\Event\Event;

class PositionEvent extends Event
{
    public function getEntityClass(): string
    {
        return Position::class;
    }
}
