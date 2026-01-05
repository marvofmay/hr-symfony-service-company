<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Role;

final class GetRolesPDFQuery
{
    public const string ROLES_UUIDS = 'rolesUUIDs';

    public function __construct(public array $rolesUUIDs = [])
    {
    }
}
