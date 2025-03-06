<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use App\Module\Company\Structure\Validator\Constraints\Department\ExistingDepartmentUUID;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'department.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'department.delete.invalidUUID'),
        new ExistingDepartmentUUID(
            message: ['uuidNotExists' => 'department.uuid.notExists', 'domain' => 'departments'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
