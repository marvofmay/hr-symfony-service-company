<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\DTO\Role;

use Symfony\Component\Validator\Constraints as Assert;
use App\Module\Company\Structure\Validator\Constraints\UniqueRoleName;

class CreateDTO
{
    #[Assert\NotBlank(message: "role.name.required")]
    #[Assert\Length(min: 3, max: 50, minMessage: 'role.name.minimum3Letters', maxMessage: 'role.name.maximum50Letters')]
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