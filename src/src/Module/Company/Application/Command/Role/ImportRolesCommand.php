<?php

declare(strict_types = 1);

namespace App\Module\Company\Application\Command\Role;

class ImportRolesCommand
{
    public function __construct(public array $data) {}
}