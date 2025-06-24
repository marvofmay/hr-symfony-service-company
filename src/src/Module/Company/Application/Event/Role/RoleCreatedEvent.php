<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;

final readonly class RoleCreatedEvent implements LoggableEventInterface
{
    public function __construct(public Role $role)
    {
    }

    public function getEntityClass(): string
    {
        return Role::class;
    }

    public function getData(): array
    {
        return [
            Role::COLUMN_NAME        => $this->role->getName(),
            Role::COLUMN_DESCRIPTION => $this->role->getDescription(),
        ];
    }
}