<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Application\Query\Role\ListRolesQuery;

final readonly class RoleListedEvent implements DomainEventInterface
{
    public function __construct(public ListRolesQuery $query) {}
}