<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;

abstract class RoleEvent implements LoggableEventInterface
{
    public function getEntityClass(): string
    {
        return Role::class;
    }

    abstract public function getData(): array;
}