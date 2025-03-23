<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Industry\UniqueIndustryName;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['name']
)]
class CreateDTO
{
    #[OA\Property(
        description: 'Nazwa tworzonej branży',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'Technologie',
    )]
    #[NotBlank(message: [
        'text' => 'industry.name.required',
        'domain' => 'industries',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'industry.name.minimumLength',
        'tooLong' => 'industry.name.maximumLength',
        'domain' => 'industries',
    ])]
    #[UniqueIndustryName]
    public string $name = '';

    #[OA\Property(
        description: 'Opcjonalny opis tworzonej branży',
        type: 'string',
        example: 'Przemysł skupiony na technologiach informatycznych.',
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
