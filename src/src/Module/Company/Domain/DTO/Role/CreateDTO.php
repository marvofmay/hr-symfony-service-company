<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\UniqueRoleName;

class CreateDTO
{
    #[NotBlank(message: [
        'text' => 'role.name.required',
        'domain' => 'roles'
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'role.name.minimumLength',
        'tooLong' => 'role.name.maximumLength',
        'domain' => 'roles'
    ])]
    #[UniqueRoleName]
    public string $name = '';

    public ?string $description = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}