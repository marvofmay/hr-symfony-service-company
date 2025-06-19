<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Role;

final readonly class GetRoleByUUIDQuery
{
    public function __construct(public string $uuid)
    {
    }
}