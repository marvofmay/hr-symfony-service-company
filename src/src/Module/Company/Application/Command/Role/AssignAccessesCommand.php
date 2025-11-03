<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Common\Domain\Interface\CommandInterface;

final readonly class AssignAccessesCommand implements CommandInterface
{
    public const string ROLE_UUID = 'roleUUID';
    public const string ACCESSES_UUIDS = 'accessesUUID';

    public function __construct(public string $roleUUID, public array $accessesUUIDs)
    {
    }
}
