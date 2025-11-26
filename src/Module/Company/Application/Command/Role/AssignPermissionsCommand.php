<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Common\Domain\Interface\CommandInterface;

final readonly class AssignPermissionsCommand implements CommandInterface
{
    public const string ROLE_UUID = 'roleUUID';
    public const string ACCESSES = 'accesses';
    public const string ACCESS_UUID = 'accessUUID';
    public const string PERMISSIONS_UUIDS = 'permissionsUUIDs';

    public function __construct(public string $roleUUID, public array $accesses)
    {
    }
}
