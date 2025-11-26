<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Application\Event\Event;

class RoleEvent extends Event
{
    public function getEntityClass(): string
    {
        return Role::class;
    }
}
