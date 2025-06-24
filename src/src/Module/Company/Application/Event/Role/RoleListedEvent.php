<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Application\Query\Role\ListRolesQuery;

final class RoleListedEvent extends RoleEvent
{
    public function __construct(public readonly ListRolesQuery $query) {}

    public function getData(): array
    {
        return ['query' => $this->query];
    }
}