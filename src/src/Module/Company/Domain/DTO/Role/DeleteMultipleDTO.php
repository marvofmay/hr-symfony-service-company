<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'role.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'role.delete.invalidUUID'),
    ])]
    public array $rolesUUIDs = [] {
        get {
            return $this->rolesUUIDs;
        }
    }
}
