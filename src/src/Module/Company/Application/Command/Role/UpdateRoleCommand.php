<?php

declare(strict_types = 1);

namespace App\module\company\Application\Command\Role;

use App\module\company\Domain\Entity\Role;

class UpdateRoleCommand
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $name,
        private readonly ?string $description,
        private readonly Role $role
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}