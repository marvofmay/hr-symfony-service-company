<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Module\Company\Structure\Validator\Constraints\Employee\ExistingEmployeeUUID;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'employee.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'employee.delete.invalidUUID'),
        new ExistingEmployeeUUID(
            message: ['uuidNotExists' => 'employee.uuid.notExists', 'domain' => 'employees'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
