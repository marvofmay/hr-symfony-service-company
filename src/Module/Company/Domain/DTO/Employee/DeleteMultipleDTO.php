<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'employee.delete.multiple.selectedUUIDsRequired')]
    public array $employeesUUIDs = [] {
        get {
            return $this->employeesUUIDs;
        }
    }
}
