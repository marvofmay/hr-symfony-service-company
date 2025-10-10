<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Department;

use App\Module\Company\Domain\Entity\Department;

final readonly class RestoreDepartmentCommand
{
    public function __construct(private Department $department)
    {
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }
}
