<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Trait\Position;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

trait PositionDTOTrait
{
    #[NotBlank(message: [
        'text' => 'position.name.required',
        'domain' => 'positions',
    ])]
    #[MinMaxLength(min: 3, max: 100, message: [
        'tooShort' => 'position.name.minimumLength',
        'tooLong' => 'position.name.maximumLength',
        'domain' => 'positions',
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

    #[Assert\Type(type: 'bool')]
    public ?bool $active = true {
        get {
            return $this->active;
        }
    }

    #[Assert\All([
        new Assert\Uuid(),
    ])]
    #[Assert\Type('array')]
    public ?array $departmentsUUIDs = [] {
        get {
            return $this->departmentsUUIDs;
        }
    }
}
