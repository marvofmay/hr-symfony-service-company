<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteMultipleRolesCommand implements CommandInterface
{
    public const string ROLES_UUIDS = 'rolesUUIDs';
    public function __construct(public array $rolesUUIDs)
    {
    }
}
