<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Application\Query\Role\ListRolesQuery;

final readonly class RoleListedEvent
{
    public function __construct(public ListRolesQuery $query) {}
}