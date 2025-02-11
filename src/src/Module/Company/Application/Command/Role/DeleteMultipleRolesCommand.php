<?php

declare(strict_types = 1);

namespace App\Module\Company\Application\Command\Role;

class DeleteMultipleRolesCommand
{
    public function __construct(public array $selectedUUID) {}
}