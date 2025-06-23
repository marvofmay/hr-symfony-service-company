<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Role;

final readonly class RoleDeletedEvent implements DomainEventInterface
{
    public function __construct(public Role $role) {}
}