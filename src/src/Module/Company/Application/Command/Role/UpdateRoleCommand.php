<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Module\Company\Domain\Entity\Role;

final readonly class UpdateRoleCommand
{
    public function __construct(
        private string $name,
        private ?string $description,
        private Role $role,
    ) {
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
