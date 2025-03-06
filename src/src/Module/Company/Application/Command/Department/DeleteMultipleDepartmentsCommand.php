<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Department;

use Doctrine\Common\Collections\Collection;

class DeleteMultipleDepartmentsCommand
{
    public function __construct(public Collection $departments)
    {
    }
}
