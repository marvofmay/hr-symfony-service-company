<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use Doctrine\Common\Collections\Collection;

class DeleteMultipleRolesCommand
{
    public function __construct(public Collection $roles)
    {
    }
}
