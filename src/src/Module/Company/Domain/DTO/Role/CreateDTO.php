<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\UniqueRoleName;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['name']
)]
class CreateDTO
{
    #[OA\Property(
        description: 'Nazwa tworzonej roli',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'Admin',
    )]
    #[NotBlank(message: [
        'text' => 'role.name.required',
        'domain' => 'roles',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'role.name.minimumLength',
        'tooLong' => 'role.name.maximumLength',
        'domain' => 'roles',
    ])]
    #[UniqueRoleName]
    public string $name = '';

    #[OA\Property(
        description: 'Opcjonalny opis tworzonej roli',
        type: 'string',
        example: 'Rola administratora',
        nullable: true
    )]
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
