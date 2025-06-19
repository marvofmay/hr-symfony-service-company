<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

final readonly class CreateRoleCommand
{
    public function __construct(private string $name, private ?string $description)
    {
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
