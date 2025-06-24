<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Domain\Entity\Role;

final class RoleCreatedEvent extends RoleEvent
{
    public function __construct(public readonly Role $role)
    {
    }

    public function getData(): array
    {
        return [
            Role::COLUMN_NAME        => $this->role->getName(),
            Role::COLUMN_DESCRIPTION => $this->role->getDescription(),
        ];
    }
}