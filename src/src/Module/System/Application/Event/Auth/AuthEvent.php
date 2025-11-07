<?php

declare(strict_types=1);

namespace App\Module\System\Application\Event\Auth;

use App\Module\Company\Application\Event\Event;

class AuthEvent extends Event
{
    public function getEntityClass(): string
    {
        return AuthEvent::class;
    }
}
