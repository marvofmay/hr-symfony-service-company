<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Module\Company\Structure\Validator\Constraints\Role\ExistingRoleUUID;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'role.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'role.delete.invalidUUID'),
        new ExistingRoleUUID(
            message: ['uuidNotExists' => 'role.uuid.notExists', 'domain' => 'roles'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
