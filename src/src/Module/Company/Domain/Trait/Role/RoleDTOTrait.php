<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Trait\Role;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;

trait RoleDTOTrait
{
    #[NotBlank(message: [
        'text' => 'role.name.required',
        'domain' => 'roles',
    ])]
    #[MinMaxLength(min: 3, max: 100, message: [
        'tooShort' => 'role.name.minimumLength',
        'tooLong' => 'role.name.maximumLength',
        'domain' => 'roles',
    ])]
    public string $name = '' {
        get {
            return $this->name;
        }
    }

    public ?string $description = null {
        get {
            return $this->description;
        }
    }
}