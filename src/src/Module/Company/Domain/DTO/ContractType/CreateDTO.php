<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\ContractType\UniqueContractTypeName;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    required: ['name']
)]
class CreateDTO
{
    #[OA\Property(
        description: 'Nazwa tworzonej formy zatrudnienia',
        type: 'string',
        maxLength: 200,
        minLength: 3,
        example: 'B2B',
    )]
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

    #[OA\Property(
        description: 'Opcjonalny opis tworzonej formy zatrudnienia.',
        type: 'string',
        example: 'Umowa cywilnoprawna zawarta między dwoma firmami.',
        nullable: true
    )]
    public ?string $description = null;

    #[OA\Property(
        description: 'Określa, czy stanowisko jest aktywne. Domyślnie wartość to true.',
        type: 'boolean',
        example: true
    )]
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
