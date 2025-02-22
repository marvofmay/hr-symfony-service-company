<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Department;

class CreateDepartmentCommand
{
    public function __construct(
        public string $name,
        public bool $active,
        public string $companyUUID,
        public ?string $parentDepartmentUUID
    ) {}
}
