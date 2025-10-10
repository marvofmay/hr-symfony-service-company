<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Domain\Interface\DTOInterface;
use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;

class CreateDTO implements DTOInterface
{
    #[NotBlank(message: [
        'text' => 'role.name.required',
        'domain' => 'roles',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
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
