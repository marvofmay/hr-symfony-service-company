<?php

namespace App\Module\Company\Application\Event\Role;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Role;

final readonly class RoleCreatedEvent implements DomainEventInterface
{
    public function __construct(public Role $role) {}
}