<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Trait\ContractType;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

trait ContractTypeDTOTrait
{
    #[NotBlank(message: [
        'text' => 'contractType.name.required',
        'domain' => 'contract_types',
    ])]
    #[MinMaxLength(min: 3, max: 100, message: [
        'tooShort' => 'contractType.name.minimumLength',
        'tooLong' => 'contractType.name.maximumLength',
        'domain' => 'contract_types',
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

    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = false {
        get {
            return $this->active;
        }
    }
}
