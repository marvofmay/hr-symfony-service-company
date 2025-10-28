<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Common\Domain\Interface\CommandInterface;

final readonly class RestoreRoleCommand implements CommandInterface
{
    public const string ROLE_UUID = 'roleUUID';

    public function __construct(public string $roleUUID)
    {
    }
}
