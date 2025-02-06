<?php

declare(strict_types = 1);

namespace App\module\company\Application\Command\Role;

class CreateRoleCommand
{
    public function __construct(public string $name, public ?string $description) {}
}