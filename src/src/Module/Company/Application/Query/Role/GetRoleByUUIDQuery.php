<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Role;

use App\Common\Domain\Interface\QueryInterface;

final readonly class GetRoleByUUIDQuery implements QueryInterface
{
    public const string ROLE_UUID = 'roleUUID';

    public function __construct(public string $roleUUID)
    {
    }
}
