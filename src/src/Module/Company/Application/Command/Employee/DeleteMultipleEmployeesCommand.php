<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Employee;

use Doctrine\Common\Collections\Collection;

class DeleteMultipleEmployeesCommand
{
    public function __construct(public Collection $employees)
    {
    }
}
