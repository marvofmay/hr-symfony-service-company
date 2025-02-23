<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Employee;

use App\Common\DTO\AddressDTO;

class CreateEmployeeCommand
{
    public function __construct(
        public string $companyUUID,
        public string $departmentUUID,
        public string $positionUUID,
        public string $contractTypeUUID,
        public string $roleUUID,
        public ?string $parentEmployeeUUID,
        public ?string $executorUUID,
        public string $firstName,
        public string $lastName,
        public string $pesel,
        public string $employmentFrom,
        public ?string $employmentTo,
        public bool $active,
        public ?array $phones,
        public AddressDTO $address
    ) {}
}
