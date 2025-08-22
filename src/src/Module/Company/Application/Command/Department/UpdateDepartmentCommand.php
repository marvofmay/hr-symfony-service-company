<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Department;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Domain\Entity\Department;

class UpdateDepartmentCommand implements CommandInterface
{
    public function __construct(
        public Department $department,
        public string     $name,
        public string     $internalCode,
        public ?string    $description,
        public bool       $active,
        public string     $companyUUID,
        public ?string    $parentDepartmentUUID,
        public ?array     $phones,
        public ?array     $emails,
        public ?array     $websites,
        public AddressDTO $address,
    )
    {
    }
}
