<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Department;

use App\Module\Company\Domain\Entity\Department;

class UpdateDepartmentCommand
{
    public function __construct(
        public Department $department,
        public string $name,
        public ?bool $active,
        public string $companyUUID,
        public ?string $parentDepartmentUUID,
    ) {}
}
