<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use Symfony\Component\Validator\Constraints as Assert;
use App\Module\Company\Structure\Validator\Constraints\Role\ExistingRoleUUID;

class UpdateDTO extends CreateDTO
{
    #[ExistingRoleUUID(
        message: ['uuidNotExists' => 'role.uuid.notExists', 'domain' => 'roles']
    )]
    #[Assert\NotBlank()]
    public string $uuid = '';

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
