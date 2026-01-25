<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Department;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'department.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'department.delete.invalidUUID'),
    ])]
    public array $departmentsUUIDs = [] {
        get {
            return $this->departmentsUUIDs;
        }
    }
}
