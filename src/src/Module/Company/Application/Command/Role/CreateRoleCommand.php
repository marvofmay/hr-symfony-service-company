<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Common\Domain\Interface\CommandInterface;

final readonly class CreateRoleCommand implements CommandInterface
{
    public const string ROLE_NAME = 'name';
    public const string ROLE_DESCRIPTION = 'description';

    public function __construct(public string $name, public ?string $description)
    {
    }
}
