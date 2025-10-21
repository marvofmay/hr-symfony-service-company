<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'department.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'department.delete.invalidUUID'),
    ])]
    public array $selectedUUIDs = [] {
        get {
            return $this->selectedUUIDs;
        }
    }
}
