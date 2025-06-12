<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\ContractType\UniqueContractTypeName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDTO
{
    #[NotBlank(message: [
        'text' => 'contractType.name.required',
        'domain' => 'contract_types',
    ])]
    #[MinMaxLength(min: 3, max: 200, message: [
        'tooShort' => 'contractType.name.minimumLength',
        'tooLong' => 'contractType.name.maximumLength',
        'domain' => 'contract_types',
    ])]
    #[UniqueContractTypeName]
    public string $name = '';

    public ?string $description = null;

    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = true;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getActive(): bool
    {
        return $this->active;
    }
}
