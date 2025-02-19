<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\DTO\Role;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'role.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'role.delete.invalidUUID')
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}