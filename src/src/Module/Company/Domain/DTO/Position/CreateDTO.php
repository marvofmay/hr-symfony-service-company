<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Position\UniquePositionName;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    required: ['name']
)]
class CreateDTO
{
    #[OA\Property(
        description: 'Nazwa tworzonego stanowiska',
        type: 'string',
        maxLength: 200,
        minLength: 3,
        example: 'Analityk systemowy',
    )]
    #[NotBlank(message: [
        'text' => 'position.name.required',
        'domain' => 'positions',
    ])]
    #[MinMaxLength(min: 3, max: 200, message: [
        'tooShort' => 'position.name.minimumLength',
        'tooLong' => 'position.name.maximumLength',
        'domain' => 'positions',
    ])]
    #[UniquePositionName]
    public string $name = '';

    #[OA\Property(
        description: 'Opcjonalny opis tworzonego stanowiska',
        type: 'string',
        example: 'Specjalista w dziedzinie technologii informatycznych, który specjalizuje się w analizowaniu, projektowaniu i wdrażaniu systemów informatycznych.',
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
