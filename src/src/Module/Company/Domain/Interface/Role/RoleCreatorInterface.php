<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

interface RoleCreatorInterface
{
    public function create(string $name, ?string $description = null): void;
}