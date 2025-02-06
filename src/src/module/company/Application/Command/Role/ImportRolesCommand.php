<?php

declare(strict_types = 1);

namespace App\module\company\Application\Command\Role;

class ImportRolesCommand
{
    public function __construct(public array $data) {}
}