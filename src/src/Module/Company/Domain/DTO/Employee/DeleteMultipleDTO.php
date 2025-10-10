<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'employee.delete.selectedRequired')]
    public array $selectedUUID = [] {
        get {
            return $this->selectedUUID;
        }
    }
}
